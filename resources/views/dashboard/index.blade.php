@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-400">Live overview of your automated trading bot</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Total Trades</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $stats['total_trades'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Win Rate</p>
            <p class="mt-1 text-2xl font-bold text-emerald-400">{{ number_format($stats['win_rate'], 1) }}%</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Total P&L</p>
            <p class="mt-1 text-2xl font-bold {{ $stats['total_pnl'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ number_format($stats['total_pnl'], 2) }} USDT
            </p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Active Strategies</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $strategies->where('status', 'active')->count() }} / {{ $strategies->count() }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
        <h2 class="text-lg font-semibold text-white">Live Market Prices</h2>
        <p class="text-xs text-slate-500">Data from Binance API</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($tickers as $ticker)
                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-white">{{ $ticker['symbol'] }}</span>
                        <span class="text-xs text-slate-500">24h</span>
                    </div>
                    <p class="mt-2 text-xl font-bold text-white">${{ number_format($ticker['price'], 2) }}</p>
                    <p class="{{ $ticker['change_percent'] >= 0 ? 'text-emerald-400' : 'text-red-400' }} text-sm">
                        {{ $ticker['change_percent'] >= 0 ? '+' : '' }}{{ number_format($ticker['change_percent'], 2) }}%
                    </p>
                    <p class="mt-1 text-xs text-slate-500">H ${{ number_format($ticker['high'], 2) }} / L ${{ number_format($ticker['low'], 2) }}</p>
                </div>
            @empty
                <p class="text-slate-500">No market data available.</p>
            @endforelse
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-lg font-semibold text-white">Portfolios</h2>
            <div class="mt-4 space-y-3">
                @forelse ($portfolios as $portfolio)
                    <a href="{{ route('portfolios.show', $portfolio) }}" class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950 px-4 py-3 hover:border-emerald-700">
                        <div>
                            <p class="font-medium text-white">{{ $portfolio->name }}</p>
                            <p class="text-xs text-slate-500">{{ $portfolio->assets->count() }} assets</p>
                        </div>
                        <span class="text-sm text-slate-400">{{ $portfolio->default_currency }}</span>
                    </a>
                @empty
                    <p class="text-slate-500">No portfolios yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-lg font-semibold text-white">Recent Trades</h2>
            <div class="mt-4 space-y-3">
                @forelse ($trades as $trade)
                    <a href="{{ route('trades.show', $trade) }}" class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950 px-4 py-3 hover:border-emerald-700">
                        <div>
                            <p class="font-medium text-white">{{ $trade->symbol }}</p>
                            <p class="text-xs text-slate-500">{{ $trade->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm {{ $trade->side === 'buy' ? 'text-emerald-400' : 'text-red-400' }}">{{ strtoupper($trade->side) }}</span>
                            <p class="text-xs text-slate-400">{{ $trade->quantity }} @ ${{ number_format($trade->price, 2) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-slate-500">No trades yet. <a href="{{ route('trades.create') }}" class="text-emerald-400 hover:underline">Place one</a>.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
