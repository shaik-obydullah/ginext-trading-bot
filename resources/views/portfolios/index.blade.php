@extends('layouts.app')

@section('title', 'Portfolios')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Portfolios</h1>
            <p class="mt-1 text-sm text-slate-400">Manage your trading portfolios and assets</p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($portfolios as $portfolio)
            <a href="{{ route('portfolios.show', $portfolio) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-5 hover:border-emerald-700">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-white">{{ $portfolio->name }}</h2>
                    <span class="rounded-full bg-slate-800 px-2 py-1 text-xs text-slate-400">{{ $portfolio->default_currency }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-400">{{ $portfolio->description }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-sm text-slate-400">{{ $portfolio->assets->count() }} assets</span>
                    <span class="text-lg font-bold text-emerald-400">
                        ${{ number_format($portfolioValues[$portfolio->id] ?? 0, 2) }}
                    </span>
                </div>
            </a>
        @empty
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-8 text-center text-slate-400">
                No portfolios yet. Create your first one below.
            </div>
        @endforelse
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <h2 class="text-lg font-semibold text-white">Create Portfolio</h2>
        <form method="POST" action="{{ route('portfolios.store') }}" class="mt-4 grid gap-4 sm:grid-cols-3">
            @csrf
            <div>
                <label class="block text-sm text-slate-300">Name</label>
                <input type="text" name="name" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm text-slate-300">Currency</label>
                <select name="default_currency" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white">
                    <option>USDT</option>
                    <option>BTC</option>
                    <option>ETH</option>
                    <option>BUSD</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-300">Description</label>
                <input type="text" name="description" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div class="sm:col-span-3">
                <button class="rounded-lg bg-emerald-600 px-6 py-2 font-medium text-white hover:bg-emerald-500">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
