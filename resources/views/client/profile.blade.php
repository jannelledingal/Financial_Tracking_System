@extends('layouts.client')

@section('content')
<div class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex flex-col gap-4">
                <div>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-950">My profile</h1>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Full name</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $client->name }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Email</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $client->email }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Account status</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $client->account_status }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Member since</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ \Illuminate\Support\Carbon::parse($client->created_at)->format('M Y') }}</p>
                    </div>
                </div>
                <div class="rounded-3xl bg-slate-50 p-6">
                    <p class="text-sm uppercase tracking-[0.24em] text-slate-500">About me</p>
                    <p class="mt-3 text-slate-700">You are a valued FinTrack client. This profile page helps you keep your contact details, account progress, and assigned consultant information in view.</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Quick actions</p>
                <div class="mt-6 space-y-3">
                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-4 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                        Account Settings
                    </a>
                    
                    <a href="{{ route('client.messages') }}" class="block rounded-2xl bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-900 hover:bg-slate-200 transition-colors">
                        Send a message
                    </a>
                    
                    <a href="{{ route('client.goals') }}" class="block rounded-2xl bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-900 hover:bg-slate-200 transition-colors">
                        Review savings goals
                    </a>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
