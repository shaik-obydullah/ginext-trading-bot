@extends('layouts.app')

@section('title', 'Strategies')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Strategies</h1>
        <p class="mt-1 text-sm text-slate-400">Configure automated trading strategies and monitor live signals</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($strategies as $strategy)
            <a href="{{ route('strategies.show', $strategy) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-5 hover:border-emerald-700">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-white">{{ $strategy->name }}</h2>
                    <span class="rounded-full px-2 py-1 text-xs {{ $strategy->status === 'active' ? 'bg-emerald-950 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                        {{ $strategy->status }}
                    </span>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-400">
                    <span class="rounded bg-slate-800 px-2 py-1">{{ str_replace('_', ' ', $strategy->type) }}</span>
                    <span class="rounded bg-slate-800 px-2 py-1">{{ $strategy->symbol }}</span>
                    <span class="rounded bg-slate-800 px-2 py-1">{{ $strategy->timeframe }}</span>
                    @if ($signals[$strategy->id] ?? null)
                        <span class="rounded px-2 py-1 font-semibold {{ $signals[$strategy->id] === 'buy' ? 'bg-emerald-950 text-emerald-400' : ($signals[$strategy->id] === 'sell' ? 'bg-red-950 text-red-400' : 'bg-slate-800 text-slate-400') }}">
                            {{ strtoupper($signals[$strategy->id]) }}
                        </span>
                    @endif
                </div>
            </a>
        @empty
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-8 text-center text-slate-400 md:col-span-2">
                No strategies yet. Create one below.
            </div>
        @endforelse
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <h2 class="text-lg font-semibold text-white">Create Strategy</h2>
        <form method="POST" action="{{ route('strategies.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-300">Name</label>
                <input type="text" name="name" placeholder="My RSI Bot" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Type</label>
                <select name="type" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
                    <option value="rsi_crossover">RSI Crossover</option>
                    <option value="macd_crossover">MACD Crossover</option>
                    <option value="grid">Grid Trading</option>
                    <option value="dca">DCA</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-300">Symbol</label>
                <input type="text" name="symbol" placeholder="BTCUSDT" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Timeframe</label>
                <select name="timeframe" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
                    <option value="5m">5m</option>
                    <option value="15m">15m</option>
                    <option value="30m">30m</option>
                    <option value="1h" selected>1h</option>
                    <option value="4h">4h</option>
                    <option value="1d">1d</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-300">RSI Period</label>
                <input type="number" name="rsi_period" value="14" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Oversold</label>
                <input type="number" name="oversold" value="30" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Overbought</label>
                <input type="number" name="overbought" value="70" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Position Size</label>
                <input type="number" step="any" name="position_size" value="0.001" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <button class="rounded-lg bg-emerald-600 px-6 py-2 font-medium text-white hover:bg-emerald-500">Create Strategy</button>
            </div>
        </form>
    </div>
</div>
@endsection
