@extends('layouts.guest')
    {{-- Internal CSS for Background & Glassmorphism --}}
    <style>
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

        .w-full.sm:max-w-md {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
    </style>
@section('content')
    <div class="mb-8 flex flex-col items-center">
        {{-- FinTrack Logo --}}
        <div class="flex items-center">
            <span class="text-5xl font-black tracking-tighter bg-gradient-to-r from-[#818cf8] via-[#60a5fa] to-[#22d3ee] bg-clip-text text-transparent filter drop-shadow-sm">
                FinTrack
            </span>
        </div>
        <p class="mt-4 text-[10px] text-slate-500 font-bold uppercase tracking-[0.3em]">Join the Financial Revolution</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-1" />
            <x-text-input id="name" class="block mt-1.5 w-full rounded-2xl border-slate-200 bg-white/50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-all" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-1" />
            <x-text-input id="email" class="block mt-1.5 w-full rounded-2xl border-slate-200 bg-white/50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-all" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@fintrack.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-1" />
                <x-text-input id="password" class="block mt-1.5 w-full rounded-2xl border-slate-200 bg-white/50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-all" type="password" name="password" required autocomplete="new-password" placeholder="Create password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-1" />
                <x-text-input id="password_confirmation" class="block mt-1.5 w-full rounded-2xl border-slate-200 bg-white/50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 transition-all" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="flex flex-col gap-5 pt-4">
            {{-- OBLONG PURPLE BUTTON --}}
            <button type="submit" 
                class="w-full relative group overflow-hidden bg-gradient-to-r from-[#6366f1] via-[#818cf8] to-[#a855f7] text-white font-bold py-4 px-8 rounded-full shadow-[0_10px_20px_-10px_rgba(168,85,247,0.5)] transition-all duration-300 hover:shadow-[0_20px_30px_-10px_rgba(168,85,247,0.6)] hover:-translate-y-1 active:scale-[0.95] uppercase text-[11px] tracking-[0.25em]">
                
                {{-- Shimmer Light Effect --}}
                <div class="absolute inset-0 w-1/2 h-full bg-white/20 skew-x-[-25deg] -translate-x-[150%] group-hover:translate-x-[250%] transition-transform duration-1000 ease-in-out"></div>
                
                <span class="relative z-10 flex items-center justify-center gap-2">
                    {{ __('Register Account') }}
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </span>
            </button>

            <div class="text-center">
                <a class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest" href="{{ route('login') }}">
                    {{ __('Already registered? Log in here') }}
                </a>
            </div>
        </div>
    </form>
@endsection