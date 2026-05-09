<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FinTrack') }} Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex bg-slate-50">
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
                            <span></span>
                        </a>___________ Admin Portal

                        <div class="mt-8 rounded-3xl bg-slate-900/80 p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500 text-sm font-semibold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-400">Administrator</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-4 pb-6">
                        <div class="space-y-6">
                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Overview</div>
                                <a href="{{ route('admin.dashboard') }}" class="mt-3 flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">⌂</span>
                                    Dashboard
                                </a>
                            </div>

                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Users</div>
                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.users') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">👥</span>
                                        User Management
                                    </a>
                                    <a href="{{ route('admin.staff-profiles') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.staff-profiles') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">👤</span>
                                        Staff Profiles
                                    </a>
                                    <a href="{{ route('admin.client-profiles') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.client-profiles') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">💼</span>
                                        Client Profiles
                                    </a>
                                    <a href="{{ route('admin.suspended-users') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.suspended-users') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">⛔</span>
                                        Suspended Users
                                    </a>
                                </div>
                            </div>

                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">System</div>
                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('admin.role-permissions') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.role-permissions') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">🔐</span>
                                        Role & Permissions
                                    </a>
                                    <a href="{{ route('admin.staff-assignments') }}" class="flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.staff-assignments') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">🔁</span>
                                        Staff Assignments
                                    </a>
                                    
                                </div>
                            </div>


                            <div>
                                <div class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Settings</div>
                                <a href="{{ route('profile.edit') }}" class="mt-3 flex items-center gap-3 rounded-3xl px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">⚙️</span>
                                    Profile Settings
                                </a>
                            </div>
                        </div>
                        <div class="border-t border-slate-800 px-6 py-5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-800 px-4 py-3 text-sm font-medium text-slate-200 hover:bg-slate-700 transition">
                                Sign Out
                            </button>
                        </form>
                    </div>
                    </div>
                </div>
            </aside>

            <main class="flex-1 bg-slate-50">
                <div class="min-h-screen px-6 py-8">
                    @if(session('success'))
                        <div class="mb-6 rounded-3xl bg-emerald-50 p-4 text-sm text-emerald-800 ring-1 ring-emerald-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded-3xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>
