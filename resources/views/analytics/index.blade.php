@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div x-data="analytics('{{ $symbol }}')" class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Analytics</h1>
            <p class="mt-1 text-sm text-slate-400">Performance metrics and technical indicators</p>
        </div>
        <div class="flex items-center space-x-2">
            <input x-model="symbol" @keydown.enter="load()"
                   class="w-40 rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none"
                   placeholder="BTCUSDT">
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Total Trades</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $stats['total_trades'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Winning / Losing</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $stats['winning_trades'] }} <span class="text-slate-500">/</span> {{ $stats['losing_trades'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Win Rate</p>
            <p class="mt-1 text-2xl font-bold text-emerald-400">{{ number_format($stats['win_rate'], 1) }}%</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Total P&L</p>
            <p class="mt-1 text-2xl font-bold {{ $stats['total_pnl'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                ${{ number_format($stats['total_pnl'], 2) }}
            </p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5 lg:col-span-2">
            <h2 class="text-lg font-semibold text-white">Price Trend (90d)</h2>
            <div class="relative mt-4 h-[420px]">
                <canvas id="trendChart" class="h-full w-full"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-lg font-semibold text-white">Portfolio Values</h2>
            <div class="mt-4 space-y-3">
                @forelse ($portfolioValues as $id => $value)
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950 px-4 py-3">
                        <span class="text-sm text-slate-300">{{ $portfolioNames[$id] ?? 'Portfolio #' . $id }}</span>
                        <span class="font-semibold text-white">${{ number_format($value, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Add assets to see portfolio values.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
        <h2 class="text-lg font-semibold text-white">Technical Indicators - <span x-text="symbol"></span></h2>
        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                <p class="text-xs text-slate-400">RSI (14)</p>
                <div class="relative mt-2 h-40">
                    <canvas id="rsiChart" class="h-full w-full"></canvas>
                </div>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                <p class="text-xs text-slate-400">MACD</p>
                <div class="relative mt-2 h-40">
                    <canvas id="macdChart" class="h-full w-full"></canvas>
                </div>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                <p class="text-xs text-slate-400">Price + SMA20</p>
                <div class="relative mt-2 h-40">
                    <canvas id="smaChart" class="h-full w-full"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function analytics(initialSymbol) {
    return {
        symbol: initialSymbol,
        charts: {},

        init() {
            this.load();
        },

        async load() {
            const [historyRes, indicatorsRes] = await Promise.all([
                fetch(`/market/chart?symbol=${encodeURIComponent(this.symbol)}&interval=1d`),
                fetch(`/market/indicators?symbol=${encodeURIComponent(this.symbol)}&interval=1d`),
            ]);

            const history = await historyRes.json();
            const ind = await indicatorsRes.json();

            const labels = history.map(d => new Date(d.time).toLocaleDateString());
            const closes = history.map(d => d.close);

            const trendChart = this.charts.trend || new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: this.symbol,
                        data: closes,
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52,211,153,0.08)',
                        fill: true,
                        pointRadius: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: '#64748b', maxTicksLimit: 10 }, grid: { color: 'rgba(51,65,85,0.3)' } },
                        y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(51,65,85,0.3)' } },
                    },
                },
            });
            this.charts.trend = trendChart;

            const draw = (id, data, color, lineLabels = labels) => {
                if (this.charts[id]) {
                    this.charts[id].data.datasets[0].data = data;
                    this.charts[id].data.labels = lineLabels;
                    this.charts[id].update();
                    return;
                }
                this.charts[id] = new Chart(document.getElementById(id), {
                    type: 'line',
                    data: {
                        labels: lineLabels,
                        datasets: [{ data, borderColor: color, pointRadius: 0, borderWidth: 1.5 }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { color: '#64748b', maxTicksLimit: 6, font: { size: 9 } }, grid: { display: false } },
                            y: { ticks: { color: '#64748b', font: { size: 9 } }, grid: { color: 'rgba(51,65,85,0.3)' } },
                        },
                    },
                });
            };

            draw('rsiChart', ind.rsi, '#f59e0b');
            draw('macdChart', ind.macd, '#8b5cf6');
            draw('smaChart', ind.sma20, '#38bdf8');
        },
    };
}
</script>
@endsection
