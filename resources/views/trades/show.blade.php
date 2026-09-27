@extends('layouts.app')

@section('title', 'Trade ' . $trade->symbol)

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">
                {{ $trade->side === 'buy' ? 'Buy' : 'Sell' }} {{ $trade->symbol }}
            </h1>
            <p class="mt-1 text-sm text-slate-400">{{ $trade->exchange }} · {{ $trade->type }} · {{ $trade->created_at->format('M d, Y H:i') }}</p>
        </div>
        @if (! in_array($trade->status, ['filled', 'cancelled', 'failed']))
            <form method="POST" action="{{ route('trades.cancel', $trade) }}">
                @csrf
                <button class="rounded-lg bg-red-950 px-4 py-2 text-sm text-red-300 hover:bg-red-900">Cancel Trade</button>
            </form>
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Status</p>
            <p class="mt-1 text-xl font-bold text-white capitalize">{{ $trade->status }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Price</p>
            <p class="mt-1 text-xl font-bold text-white">${{ number_format($trade->price, 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Total</p>
            <p class="mt-1 text-xl font-bold text-white">${{ number_format($trade->total, 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Fee</p>
            <p class="mt-1 text-xl font-bold text-white">${{ number_format($trade->fee, 4) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-lg font-semibold text-white">Details</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between border-b border-slate-800/60 pb-2">
                    <dt class="text-slate-400">Quantity</dt>
                    <dd class="font-medium text-white">{{ $trade->quantity }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-800/60 pb-2">
                    <dt class="text-slate-400">Executed at</dt>
                    <dd class="font-medium text-white">{{ $trade->executed_at?->format('M d, Y H:i') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-800/60 pb-2">
                    <dt class="text-slate-400">Portfolio</dt>
                    <dd class="font-medium text-white">{{ $trade->portfolio->name ?? 'None' }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-800/60 pb-2">
                    <dt class="text-slate-400">Strategy</dt>
                    <dd class="font-medium text-white">{{ $trade->strategy->name ?? 'Manual' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-lg font-semibold text-white">Orders</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-slate-500">
                        <tr class="border-b border-slate-800">
                            <th class="pb-2">Type</th>
                            <th class="pb-2">Status</th>
                            <th class="pb-2">Filled</th>
                            <th class="pb-2">Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trade->orders as $order)
                            <tr class="border-b border-slate-800/60">
                                <td class="py-3">{{ $order->type }}</td>
                                <td class="py-3"><span class="rounded-full bg-slate-800 px-2 py-0.5 text-xs">{{ $order->status }}</span></td>
                                <td class="py-3">{{ $order->filled_quantity }}</td>
                                <td class="py-3">{{ $order->remaining_quantity }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-3 text-slate-500" colspan="4">No orders.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
