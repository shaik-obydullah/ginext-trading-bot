@extends('layouts.app')

@section('title', $portfolio->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $portfolio->name }}</h1>
            <p class="mt-1 text-sm text-slate-400">{{ $portfolio->description }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <form method="POST" action="{{ route('portfolios.destroy', $portfolio) }}"
                  onsubmit="return confirm('Delete this portfolio?')">
                @csrf
                @method('DELETE')
                <button class="rounded-lg bg-red-950 px-4 py-2 text-sm text-red-300 hover:bg-red-900">Delete</button>
            </form>
        </div>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <h2 class="text-lg font-semibold text-white">Add Asset</h2>
        <form method="POST" action="{{ route('portfolios.assets.store', $portfolio) }}" class="mt-4 grid gap-4 sm:grid-cols-3">
            @csrf
            <div>
                <label class="block text-sm text-slate-300">Symbol</label>
                <input type="text" name="symbol" placeholder="BTCUSDT" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Quantity</label>
                <input type="number" step="any" min="0" name="quantity" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Avg Buy Price</label>
                <input type="number" step="any" min="0" name="avg_buy_price" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div class="sm:col-span-3">
                <button class="rounded-lg bg-emerald-600 px-6 py-2 font-medium text-white hover:bg-emerald-500">Add Asset</button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
        <h2 class="text-lg font-semibold text-white">Assets</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-800">
                        <th class="pb-2">Symbol</th>
                        <th class="pb-2">Quantity</th>
                        <th class="pb-2">Avg Buy</th>
                        <th class="pb-2">Current Price</th>
                        <th class="pb-2">Value</th>
                        <th class="pb-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($portfolio->assets as $asset)
                        <tr class="border-b border-slate-800/60">
                            <td class="py-3 font-medium text-white">{{ $asset->symbol }}</td>
                            <td class="py-3">{{ $asset->quantity }}</td>
                            <td class="py-3">${{ number_format($asset->avg_buy_price, 2) }}</td>
                            <td class="py-3">${{ isset($values[$asset->symbol]) ? number_format($values[$asset->symbol], 2) : '-' }}</td>
                            <td class="py-3 text-emerald-400">
                                @if (isset($values[$asset->symbol]))
                                    ${{ number_format($asset->quantity * $values[$asset->symbol], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3">
                                <form method="POST" action="{{ route('portfolios.assets.destroy', [$portfolio, $asset]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs text-red-400 hover:text-red-300">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-slate-500" colspan="6">No assets yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
        <h2 class="text-lg font-semibold text-white">Trades</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs text-slate-500">
                    <tr class="border-b border-slate-800">
                        <th class="pb-2">Symbol</th>
                        <th class="pb-2">Side</th>
                        <th class="pb-2">Type</th>
                        <th class="pb-2">Qty</th>
                        <th class="pb-2">Price</th>
                        <th class="pb-2">Status</th>
                        <th class="pb-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($portfolio->trades as $trade)
                        <tr class="border-b border-slate-800/60">
                            <td class="py-3"><a href="{{ route('trades.show', $trade) }}" class="font-medium text-white hover:text-emerald-400">{{ $trade->symbol }}</a></td>
                            <td class="py-3 {{ $trade->side === 'buy' ? 'text-emerald-400' : 'text-red-400' }}">{{ strtoupper($trade->side) }}</td>
                            <td class="py-3 text-slate-400">{{ $trade->type }}</td>
                            <td class="py-3">{{ $trade->quantity }}</td>
                            <td class="py-3">${{ number_format($trade->price, 2) }}</td>
                            <td class="py-3"><span class="rounded-full bg-slate-800 px-2 py-0.5 text-xs">{{ $trade->status }}</span></td>
                            <td class="py-3 text-slate-400">{{ $trade->created_at->format('M d, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-slate-500" colspan="7">No trades yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
