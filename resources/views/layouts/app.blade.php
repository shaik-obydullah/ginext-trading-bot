<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="h-full text-slate-200 antialiased">
<div class="min-h-full">
    <nav class="border-b border-slate-800 bg-slate-900/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="flex items-center py-4">
                    <span class="text-xl font-bold text-emerald-400">Ginext</span>
                    <span class="ml-1 text-xl font-light text-slate-400">Bot</span>
                </a>
                <div class="hidden space-x-6 md:flex">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} text-sm font-medium">Dashboard</a>
                    <a href="{{ route('market.index') }}" class="{{ request()->routeIs('market.*') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} text-sm font-medium">Market</a>
                    <a href="{{ route('portfolios.index') }}" class="{{ request()->routeIs('portfolios.*') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} text-sm font-medium">Portfolios</a>
                    <a href="{{ route('strategies.index') }}" class="{{ request()->routeIs('strategies.*') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} text-sm font-medium">Strategies</a>
                    <a href="{{ route('trades.index') }}" class="{{ request()->routeIs('trades.*') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} text-sm font-medium">Trades</a>
                    <a href="{{ route('analytics.index') }}" class="{{ request()->routeIs('analytics.*') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} text-sm font-medium">Analytics</a>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @auth
                    <span class="hidden text-sm text-slate-400 sm:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-slate-300 hover:bg-slate-700">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-emerald-800 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-800 bg-red-950/50 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @isset($errors)
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-800 bg-red-950/50 px-4 py-3 text-sm text-red-300">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endisset

        @yield('content')
    </main>
</div>
</body>
</html>
