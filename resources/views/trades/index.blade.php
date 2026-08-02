@extends('layouts.app')

@section('title', 'Trades')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Trades</h1>
            <p class="mt-1 text-sm text-slate-400">Trade execution history</p>
        </div>
        <a href="{{ route('trades.create') }}" class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-500">New Trade</a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3">
        <select name="symbol" class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white">
            <option value="">All Symbols</option>
            @foreach ($symbols as $s)
                <option value="{{ $s }}" {{ request('symbol') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <select name="side" class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white">
            <option value="">All Sides</option>
            <option value="buy" {{ request('side') === 'buy' ? 'selected' : '' }}>Buy</option>
            <option value="sell" {{ request('side') === 'sell' ? 'selected' : '' }}>Sell</option>
        </select>
        <select name="status" class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white">
            <option value="">All Statuses</option>
            @foreach (['pending', 'open', 'filled', 'partial', 'cancelled', 'failed'] as $st)
                <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-700">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900">
        <table class="w-full text-left text-sm">
            <thead class="text-xs text-slate-500">
                <tr class="border-b border-slate-800">
                    <th class="px-4 py-3">Symbol</th>
                    <th class="px-4 py-3">Side</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Fee</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trades as $trade)
                    <tr class="border-b border-slate-800/60 hover:bg-slate-800/30">
                        <td class="px-4 py-3"><a href="{{ route('trades.show', $trade) }}" class="font-medium text-white hover:text-emerald-400">{{ $trade->symbol }}</a></td>
                        <td class="px-4 py-3 {{ $trade->side === 'buy' ? 'text-emerald-400' : 'text-red-400' }}">{{ strtoupper($trade->side) }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $trade->type }}</td>
                        <td class="px-4 py-3">{{ $trade->quantity }}</td>
                        <td class="px-4 py-3">${{ number_format($trade->price, 2) }}</td>
                        <td class="px-4 py-3">${{ number_format($trade->total, 2) }}</td>
                        <td class="px-4 py-3 text-slate-400">${{ number_format($trade->fee, 4) }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-800 px-2 py-0.5 text-xs">{{ $trade->status }}</span></td>
                        <td class="px-4 py-3 text-slate-400">{{ $trade->created_at->format('M d, H:i') }}</td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-center text-slate-500" colspan="9">No trades found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $trades->links() }}</div>
</div>
@endsection
