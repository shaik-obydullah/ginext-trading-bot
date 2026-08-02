@extends('layouts.app')

@section('title', $strategy->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $strategy->name }}</h1>
            <p class="mt-1 text-sm text-slate-400">
                {{ str_replace('_', ' ', $strategy->type) }} on {{ $strategy->symbol }} ({{ $strategy->timeframe }})
            </p>
        </div>
        <div class="flex items-center space-x-3">
            @if ($strategy->status === 'active')
                <form method="POST" action="{{ route('strategies.deactivate', $strategy) }}">
                    @csrf
                    <button class="rounded-lg bg-amber-950 px-4 py-2 text-sm text-amber-300 hover:bg-amber-900">Deactivate</button>
                </form>
            @else
                <form method="POST" action="{{ route('strategies.activate', $strategy) }}">
                    @csrf
                    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Activate</button>
                </form>
            @endif
            <form method="POST" action="{{ route('strategies.destroy', $strategy) }}" onsubmit="return confirm('Delete strategy?')">
                @csrf
                @method('DELETE')
                <button class="rounded-lg bg-red-950 px-4 py-2 text-sm text-red-300 hover:bg-red-900">Delete</button>
            </form>
        </div>
    </div>

    @if ($signal)
        <div class="rounded-xl border px-5 py-4 {{ $signal === 'buy' ? 'border-emerald-800 bg-emerald-950/50 text-emerald-300' : ($signal === 'sell' ? 'border-red-800 bg-red-950/50 text-red-300' : 'border-slate-800 bg-slate-900 text-slate-300') }}">
            Current signal: <strong>{{ strtoupper($signal) }}</strong>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
            <h2 class="text-lg font-semibold text-white">Parameters</h2>
            <dl class="mt-4 space-y-2 text-sm">
                @forelse ($strategy->parameters_json ?? [] as $key => $value)
                    <div class="flex justify-between border-b border-slate-800/60 pb-2">
                        <dt class="text-slate-400">{{ str_replace('_', ' ', $key) }}</dt>
                        <dd class="font-medium text-white">{{ $value }}</dd>
                    </div>
                @empty
                    <p class="text-slate-500">No parameters set.</p>
                @endforelse
            </dl>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
            <h2 class="text-lg font-semibold text-white">Update</h2>
            <form method="POST" action="{{ route('strategies.update', $strategy) }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm text-slate-300">Name</label>
                    <input type="text" name="name" value="{{ $strategy->name }}" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-slate-300">Symbol</label>
                    <input type="text" name="symbol" value="{{ $strategy->symbol }}" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-slate-300">Timeframe</label>
                    <select name="timeframe" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
                        @foreach (['5m', '15m', '30m', '1h', '4h', '1d'] as $tf)
                            <option value="{{ $tf }}" {{ $strategy->timeframe === $tf ? 'selected' : '' }}>{{ $tf }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-300">Position Size</label>
                    <input type="number" step="any" name="position_size" value="{{ $strategy->parameters_json['position_size'] ?? 0.001 }}" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <button class="rounded-lg bg-emerald-600 px-6 py-2 font-medium text-white hover:bg-emerald-500">Save</button>
                </div>
            </form>
        </div>
    </div>

    @if (! empty($candles))
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-lg font-semibold text-white">Recent Price Action ({{ $strategy->symbol }})</h2>
            <div class="relative mt-4 h-[320px]">
                <canvas id="strategyChart" class="h-full w-full"></canvas>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const candles = @json(array_column($candles, 'close'));
            const labels = @json(array_map(fn ($c) => date('M d H:i', $c['time'] / 1000), $candles));
            new Chart(document.getElementById('strategyChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Close',
                        data: candles,
                        borderColor: '#34d399',
                        pointRadius: 0,
                        borderWidth: 1.5,
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
        });
        </script>
    @endif
</div>
@endsection
