@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="mx-auto mt-16 max-w-md">
    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-8">
        <h1 class="text-2xl font-bold text-white">Create account</h1>
        <p class="mt-1 text-sm text-slate-400">Start trading with Ginext Trading Bot</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-300">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300">Password</label>
                <input type="password" name="password" required
                       class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300">Confirm password</label>
                <input type="password" name="password_confirmation" required
                       class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none">
            </div>
            <button class="w-full rounded-lg bg-emerald-600 py-2 font-medium text-white hover:bg-emerald-500">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Already have an account? <a href="{{ route('login') }}" class="text-emerald-400 hover:underline">Sign in</a>
        </p>
    </div>
</div>
@endsection
