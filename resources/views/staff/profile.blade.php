@extends('layouts.staff')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Profile</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">My profile</h1>
            <p class="mt-2 text-sm text-slate-500">View your staff profile and update your contact details.</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Account Settings</a>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-sky-500 text-3xl font-semibold text-white">{{ strtoupper(substr($staff->name, 0, 2)) }}</div>
                    <div>
                        <p class="text-2xl font-semibold text-slate-950">{{ $staff->name }}</p>
                        <p class="text-sm text-slate-500">Staff · {{ $staff->email }}</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Joined</p>
                        <p class="mt-3 text-lg font-semibold text-slate-950">{{ $staff->created_at?->format('F j, Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Status</p>
                        <p class="mt-3 inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">{{ $staff->account_status }}</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-slate-50 p-5">
                    <p class="text-sm uppercase tracking-[0.24em] text-slate-500">About</p>
                    <p class="mt-3 text-sm leading-7 text-slate-700">You are part of the staff team responsible for managing client accounts, logging transactions, and preparing financial reports. Use this portal to monitor assigned clients and keep communications current.</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Contact</p>
                <div class="mt-5 space-y-4 text-sm text-slate-600">
                    <div class="flex items-center justify-between gap-4 rounded-3xl bg-slate-50 p-4">
                        <p>Email</p>
                        <p class="font-semibold text-slate-950">{{ $staff->email }}</p>
                    </div>
                    
                    <div class="flex items-center justify-between gap-4 rounded-3xl bg-slate-50 p-4">
                        <p>Role</p>
                        <p class="font-semibold text-slate-950">{{ $staff->role }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
