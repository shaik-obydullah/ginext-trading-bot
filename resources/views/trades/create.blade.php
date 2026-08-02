@extends('layouts.app')

@section('title', 'New Trade')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-white">New Trade</h1>
        <p class="mt-1 text-sm text-slate-400">Execute a market or limit order at the current live price</p>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <form method="POST" action="{{ route('trades.store') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label class="block text-sm text-slate-300">Symbol</label>
                <input type="text" name="symbol" placeholder="BTCUSDT" required value="{{ old('symbol') }}"
                       class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Side</label>
                <select name="side" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
                    <option value="buy">Buy</option>
                    <option value="sell">Sell</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-300">Type</label>
                <select name="type" id="orderType" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
                    <option value="market">Market</option>
                    <option value="limit">Limit</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-300">Portfolio</label>
                <select name="portfolio_id" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
                    <option value="">None</option>
                    @foreach ($portfolios as $portfolio)
                        <option value="{{ $portfolio->id }}">{{ $portfolio->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-300">Quantity</label>
                <input type="number" step="any" min="0.00000001" name="quantity" required value="{{ old('quantity') }}"
                       class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Limit Price</label>
                <input type="number" step="any" min="0" name="price" id="limitPrice" disabled value="{{ old('price') }}"
                       class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div class="sm:col-span-2">
                <button class="w-full rounded-lg bg-emerald-600 py-2 font-medium text-white hover:bg-emerald-500">Execute Trade</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('orderType').addEventListener('change', (e) => {
    document.getElementById('limitPrice').disabled = e.target.value !== 'limit';
});
</script>
@endsection
