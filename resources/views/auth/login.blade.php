@extends('layouts.guest')

    {{-- Internal CSS for the Finance Background --}}
    <style>
        /* This targets the main wrapper provided by x-guest-layout */
        .min-h-screen {
            background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), 
                              url('https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Adjusting the default Laravel white box to be semi-transparent */
        .w-full.sm:max-w-md {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1.5rem; /* Matches your rounded-xl theme */
        }
    </style>
@section('content')
    <div class="mb-8 flex flex-col items-center">
        
        <p class="mt-4 text-sm text-slate-500 font-bold uppercase tracking-tight">FinTrack Login Area</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-xs font-bold uppercase tracking-wider text-slate-600" />
            <x-text-input id="email" 
                class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3" 
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="text-xs font-bold uppercase tracking-wider text-slate-600" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-indigo-600 hover:text-indigo-500 transition" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" 
                class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3"
                type="password"
                name="password"
                required autocomplete="current-password" 
                placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="pt-2">
            {{-- Brand Blue Button --}}
            <button type="submit" 
    class="w-full relative group overflow-hidden bg-gradient-to-r from-[#6366f1] to-[#a855f7] text-white font-bold py-4 px-8 rounded-full shadow-[0_10px_20px_-10px_rgba(168,85,247,0.5)] transition-all duration-300 hover:shadow-[0_20px_30px_-10px_rgba(168,85,247,0.6)] hover:-translate-y-1 active:scale-[0.95] uppercase text-xs tracking-[0.2em]">
    
    {{-- Shimmer Light Effect --}}
    <div class="absolute inset-0 w-1/2 h-full bg-white/20 skew-x-[-25deg] -translate-x-[150%] group-hover:translate-x-[250%] transition-transform duration-1000 ease-in-out"></div>
    
    <span class="relative z-10 flex items-center justify-center gap-2">
        {{ __('Log in') }}
        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </span>
</button>
        </div>
    </form>
@endsection
