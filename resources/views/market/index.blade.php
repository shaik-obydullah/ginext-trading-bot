@extends('layouts.app')

@section('title', 'Market')

@section('content')
<div x-data="marketChart('{{ $symbol }}', '{{ $interval }}')" class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Market</h1>
            <p class="mt-1 text-sm text-slate-400">Live candlestick data from Binance</p>
        </div>
        <div class="flex items-center space-x-2">
            <input x-model="symbol" @keydown.enter="load()"
                   class="w-40 rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none"
                   placeholder="BTCUSDT">
            <select x-model="interval" @change="load()" class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white">
                <option value="5m">5m</option>
                <option value="15m">15m</option>
                <option value="30m">30m</option>
                <option value="1h">1h</option>
                <option value="4h">4h</option>
                <option value="1d">1d</option>
            </select>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
            <p class="text-xs text-slate-400">Current Price</p>
            <p class="mt-1 text-xl font-bold text-white">$<span x-text="ticker.price != null ? Number(ticker.price).toLocaleString() : '-'"></span></p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
            <p class="text-xs text-slate-400">24h Change</p>
            <p class="mt-1 text-xl font-bold" :class="ticker.change_percent >= 0 ? 'text-emerald-400' : 'text-red-400'">
                <span x-text="ticker.change_percent != null ? (ticker.change_percent >= 0 ? '+' : '') + Number(ticker.change_percent).toFixed(2) + '%' : '-'"></span>
            </p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
            <p class="text-xs text-slate-400">24h High</p>
            <p class="mt-1 text-xl font-bold text-white">$<span x-text="ticker.high != null ? Number(ticker.high).toLocaleString() : '-'"></span></p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
            <p class="text-xs text-slate-400">24h Low</p>
            <p class="mt-1 text-xl font-bold text-white">$<span x-text="ticker.low != null ? Number(ticker.low).toLocaleString() : '-'"></span></p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-lg font-semibold text-white" x-text="symbol"></h2>
            <div class="flex flex-wrap gap-2">
                <template x-for="s in ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT', 'XRPUSDT', 'ADAUSDT', 'DOGEUSDT', 'LINKUSDT']" :key="s">
                    <button @click="symbol = s; load()"
                            class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-300 hover:border-emerald-600"
                            :class="s === symbol ? 'border-emerald-600 text-emerald-400' : ''" x-text="s"></button>
                </template>
            </div>
        </div>
        <div class="relative h-[420px]">
            <canvas id="priceChart" class="h-full w-full"></canvas>
        </div>
    </div>
</div>

<script>
function marketChart(initialSymbol, initialInterval) {
    return {
        symbol: initialSymbol,
        interval: initialInterval,
        ticker: {},
        chart: null,
        polling: null,

        init() {
            this.load();
            this.polling = setInterval(() => this.loadTicker(), 15000);
            window.addEventListener('beforeunload', () => clearInterval(this.polling));
        },

        async load() {
            await Promise.all([this.loadChart(), this.loadTicker()]);
        },

        async loadChart() {
            const res = await fetch(`/market/chart?symbol=${encodeURIComponent(this.symbol)}&interval=${this.interval}`);
            const data = await res.json();

            const labels = data.map(d => new Date(d.time).toLocaleString());
            const closes = data.map(d => d.close);

            if (this.chart) {
                this.chart.data.labels = labels;
                this.chart.data.datasets[0].data = closes;
                this.chart.update();
                return;
            }

            this.chart = new Chart(document.getElementById('priceChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: this.symbol,
                        data: closes,
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52, 211, 153, 0.08)',
                        fill: true,
                        pointRadius: 0,
                        borderWidth: 1.5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: '#64748b', maxTicksLimit: 8 }, grid: { color: 'rgba(51, 65, 85, 0.3)' } },
                        y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(51, 65, 85, 0.3)' } },
                    },
                },
            });
        },

        async loadTicker() {
            const res = await fetch(`/market/ticker?symbol=${encodeURIComponent(this.symbol)}`);
            this.ticker = await res.json();
        },
    };
}
</script>
@endsection
