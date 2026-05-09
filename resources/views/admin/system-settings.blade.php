@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-semibold text-slate-950">Admin Profile Settings</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-500">Update your profile information, security preferences, and notification settings.</p>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <h2 class="text-lg font-semibold text-slate-950">Settings overview</h2>
        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <div class="rounded-3xl bg-slate-50 p-5">
                <p class="text-sm text-slate-500">Authentication</p>
                <p class="mt-3 text-base font-semibold text-slate-950">Two-factor login, password policies, session timeouts.</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-5">
                <p class="text-sm text-slate-500">Notifications</p>
                <p class="mt-3 text-base font-semibold text-slate-950">Email alerts, reports, and admin announcements.</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-5">
                <p class="text-sm text-slate-500">Branding</p>
                <p class="mt-3 text-base font-semibold text-slate-950">Portal name, logo, and theme settings.</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-5">
                <p class="text-sm text-slate-500">Integrations</p>
                <p class="mt-3 text-base font-semibold text-slate-950">Connected finance tools and third-party APIs.</p>
            </div>
        </div>
    </div>
</div>
@endsection
