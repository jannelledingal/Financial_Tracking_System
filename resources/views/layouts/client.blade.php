<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FinTrack') }} Client</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div class="min-h-screen flex bg-slate-100 text-slate-900">
            <aside class="w-72 shrink-0 bg-slate-950 text-slate-100">
                <div class="flex h-full flex-col">
                    <div class="px-6 py-8">
                        <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-3 text-white text-xl font-semibold">
                            <h1 class="text-4xl font-black tracking-tighter transition-all duration-300 hover:scale-105" 
                                style="
                                    background: linear-gradient(to right, #818cf8, #60a5fa, #22d3ee);
                                    -webkit-background-clip: text;
                                    -webkit-text-fill-color: transparent;
                                    display: block;
                                    filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.2)) drop-shadow(0 5px 10px rgba(0,0,0,0.9));
                                ">
                                FinTrack
                            </h1>
                        </a>
                        <p class="mt-1 text-xs font-medium uppercase tracking-widest text-slate-500">Client Portal</p>

                        <div class="mt-8 rounded-3xl bg-slate-900/90 p-5 shadow-lg ring-1 ring-white/10">
                            <div class="flex items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-sky-500 text-lg font-semibold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Client</p>
                                    <p class="text-lg font-semibold text-white">{{ Auth::user()->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <nav class="flex-1 px-3 pb-6">
                        <div class="space-y-2">
                            <p class="px-4 text-xs uppercase tracking-[0.3em] text-slate-500">Overview</p>
                            <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('client.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Dashboard</a>
                        </div>

                        <div class="mt-6 space-y-2">
                            <p class="px-4 text-xs uppercase tracking-[0.3em] text-slate-500">Finance</p>
                            <a href="{{ route('client.income') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('client.income') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Income</a>
                            <a href="{{ route('client.expenses') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('client.expenses') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Expenses</a>
                            <a href="{{ route('client.accounts') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('client.accounts') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">My Accounts</a>
                        </div>

                        <div class="mt-6 space-y-2">
                            <p class="px-4 text-xs uppercase tracking-[0.3em] text-slate-500">Reports</p>
                            
                            <a href="{{ route('client.transaction-history') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('client.transaction-history') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Transaction History</a>
                        </div>

                        <div class="mt-6 space-y-2">
                            <p class="px-4 text-xs uppercase tracking-[0.3em] text-slate-500">Support</p>
                            <a href="{{ route('client.messages') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('client.messages') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Messages</a>
                            <a href="{{ route('client.profile') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('client.profile') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">My Profile</a>
                        </div>

                        <div class="mt-8 bg-slate-950 px-4 py-5 border-t border-white/5">
                            <p class="px-2 text-xs uppercase tracking-[0.3em] text-slate-500">Your assigned consultant</p>
                            
                            @php
                                $assignment = Auth::user()->first_assigned_staff;
                            @endphp

                            @if($assignment && $assignment->staffProfile && $assignment->staffProfile->user)
                                <div class="mt-4 rounded-3xl bg-slate-900 p-4 ring-1 ring-white/5">
                                    <p class="font-semibold text-slate-100">{{ $assignment->staffProfile->user->name }}</p>
                                    <p class="mt-1 text-sm text-slate-400">Finance consultant</p>
                                    <p class="mt-2 text-xs text-slate-500">
                                        Assigned {{ $assignment->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            @else
                                <div class="mt-4 rounded-3xl bg-slate-900/50 p-4 border border-dashed border-slate-800">
                                    <p class="text-xs text-slate-500 italic text-center">Pending consultant assignment</p>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="mt-5">
                                @csrf
                                <button type="submit" class="w-full rounded-3xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 shadow-sm hover:bg-slate-100 transition-colors">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </aside>

            <div class="flex-1 flex flex-col min-w-0">
                @include('layouts.navigation-top')
                <main class="flex-1 p-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>