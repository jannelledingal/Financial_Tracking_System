@extends('layouts.admin')

@section('content')
<style>
    .page-bg-custom {
        background-image: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95)), 
                          url('https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        border-radius: 1rem;
        margin-top: 1rem;
        padding: 1rem;
        min-height: 85vh;
    }
    /* Glass effect to make content readable over the background image */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>

<div class="page-bg-custom">
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col gap-2">
            <p class="text-xs uppercase tracking-[0.3em] font-bold text-indigo-400">System Overview</p>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold text-white tracking-tight">Admin Portal</h1>
                    <p class="text-slate-400 mt-1">Logged in as <span class="font-semibold text-indigo-300">{{ Auth::user()->name }}</span> • Admin Access Granted</p>
                </div>
                <div class="hidden md:block">
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-400 ring-1 ring-inset ring-emerald-500/20">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        System Online
                    </span>
                </div>
            </div>
        </div>

        {{-- Enhanced Image & Description Section --}}
        <div class="overflow-hidden rounded-[1rem] glass-card shadow-2xl">
            <div class="flex flex-col lg:flex-row">
                {{-- Image Container --}}
                <div class="lg:w-1/2">
                    <img 
                        src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2000&auto=format&fit=crop" 
                        alt="Financial Dashboard Analytics" 
                        class="h-full w-full object-cover min-h-[350px] lg:min-h-[500px] opacity-90 hover:opacity-100 transition-opacity"
                    >
                </div>
                
                {{-- Description Content --}}
                <div class="flex flex-col justify-center p-8 lg:w-1/2 lg:p-12">
                    <div class="space-y-6">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        
                        <div>
                            <h2 class="text-3xl font-bold text-white">Total System Integrity</h2>
                            <p class="mt-4 leading-relaxed text-slate-300 text-lg">
                                Welcome to the central administrative core of FinTrack. This interface provides high-level oversight of financial accounts, staff assignments, and user activities. 
                            </p>
                        </div>

                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-slate-300">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
                                    <span class="text-xs">✔</span>
                                </div>
                                Real-time User Management
                            </li>
                            <li class="flex items-center gap-3 text-slate-300">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
                                    <span class="text-xs">✔</span>
                                </div>
                                Staff-to-Client Relationship Monitoring
                            </li>
                        </ul>

                        <div class="pt-6">
                            <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-8 py-4 text-sm font-bold text-white transition-all hover:bg-indigo-500 hover:shadow-[0_0_20px_rgba(79,70,229,0.4)] active:scale-95">
                                Launch User Manager
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Metrics Stats --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div class="rounded-[1rem] glass-card p-8">
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">Client Base</p>
                <p class="mt-2 text-3xl font-extrabold text-white">{{ \App\Models\User::where('role', 'Client')->count() }}</p>
                <p class="text-sm text-slate-400 mt-1">Active Accounts</p>
            </div>

            <div class="rounded-[1rem] glass-card p-8">
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">Internal Team</p>
                <p class="mt-2 text-3xl font-extrabold text-white">{{ \App\Models\User::where('role', 'Staff')->count() }}</p>
                <p class="text-sm text-slate-400 mt-1">Certified Staff</p>
            </div>
        </div>
    </div>
</div>
@endsection