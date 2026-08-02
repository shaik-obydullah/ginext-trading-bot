@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
<div class="mx-auto mt-16 max-w-md">
    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-8">
        <h1 class="text-2xl font-bold text-white">Sign in</h1>
        <p class="mt-1 text-sm text-slate-400">Welcome back to Ginext Trading Bot</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300">Password</label>
                <input type="password" name="password" required
                       class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <button class="w-full rounded-lg bg-emerald-600 py-2 font-medium text-white hover:bg-emerald-500">
                Sign in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Demo login: <code class="text-slate-300">admin@example.com / password</code>
        </p>

        <p class="mt-2 text-center text-sm text-slate-400">
            No account? <a href="{{ route('register') }}" class="text-emerald-400 hover:underline">Register</a>
        </p>
    </div>
</div>
@endsection
