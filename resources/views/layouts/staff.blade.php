<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FinTrack') }} Staff</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div class="min-h-screen flex bg-slate-100 text-slate-900">
            <aside class="w-72 shrink-0 bg-slate-950 text-slate-100">
                <div class="flex h-full flex-col">
                    <div class="px-6 py-8">
                        <div class="flex items-center gap-3 text-white text-xl font-semibold">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-200 text-slate-950 text-lg font-bold">SP</span>
                            <div>
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
                                <p class="text-lg font-semibold">Staff Portal</p>
                            </div>
                        </div>

                        <div class="mt-8 rounded-[32px] border border-slate-800/70 bg-slate-900/90 p-5 shadow-lg">
                            <div class="flex items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-sky-500 text-lg font-semibold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                                <div>
                                    <p class="text-lg font-semibold text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-slate-400">Senior Finance Consultant</p>
                                </div>
                            </div>
                            <div class="mt-6 space-y-2 text-sm text-slate-400">
                                <div>Staff ID: FT-00{{ Auth::user()->id }}</div>
                                <div>Active since {{ Auth::user()->created_at?->format('M Y') ?? 'N/A' }}</div>
                            </div>
                    
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-4 pb-6">
                        <div class="space-y-6">
                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Workspace</div>
                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Dashboard</a>
                                    <a href="{{ route('staff.clients') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.clients') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">My Clients</a>
                                </div>
                            </div>

                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Transactions</div>
                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('staff.add-transaction') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.add-transaction') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Add Transaction & Account</a>
                                    <a href="{{ route('staff.transaction-log') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.transaction-log') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Transaction Log</a>
                                    <a href="{{ route('staff.income-records') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.income-records') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Income Records</a>
                                    <a href="{{ route('staff.expense-records') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.expense-records') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Expense Records</a>
                                </div>
                            </div>

                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Reports & Comms</div>
                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('staff.generate-report') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.generate-report') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Generate Report</a>
                                    <a href="{{ route('staff.client-messages') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.client-messages') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Client Messages</a>
                                </div>
                            </div>

                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Profile</div>
                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('staff.profile') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('staff.profile') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">My Profile</a>
                                </div>
                            </div>
                        </div>


                        <div class="border-t border-slate-800 px-6 py-5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-800 px-4 py-3 text-sm font-medium text-slate-200 hover:bg-slate-700 transition">Sign Out</button>
                            </form>
                        </div>
                    </div>


                </div>
            </aside>

                <div class="flex-1 flex flex-col min-w-0">
                
                    @include('layouts.navigation-top')

                    <main class="flex-1 p-6">
                        @if(session('success') || session('error'))
                            <div class="mb-6 space-y-3">
                                @if(session('success'))
                                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900 shadow-sm">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-900 shadow-sm">
                                        {{ session('error') }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        @yield('content')
                    </main>
                </div>
        </div>
    </body>
</html>
