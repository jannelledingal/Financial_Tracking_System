@extends('layouts.staff')
<style>
    .page-bg-custom {
        background-image: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), 
                          url('https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        border-radius: 1rem;
        margin-top: 1rem;
        padding: 2rem;
    }
   
</style>


@section('content')

<div class="page-bg-custom">
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Staff workspace</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">{{ $staff->name }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ now()->format('l, F j, Y') }} · {{ $totalClients }} active clients assigned</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.add-transaction') }}" class="rounded-full bg-slate-200 px-5 py-3 text-sm font-semibold text-gray shadow-sm hover:bg-slate-800">Add transaction</a>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-4">
        <div class="text-center text-sm text-slate-500">-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Total clients</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $totalClients }}</p>
            <p class="mt-3 text-sm text-slate-500"><span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">+1 new this month</span></p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Transactions logged</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $transactionsLogged }}</p>
            <p class="mt-3 text-sm text-slate-500"><span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">+{{ max(0, $transactionsLogged - 210) }} this month</span></p>
        </div>
        <div class="text-center text-sm text-slate-500">-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>
    </div>
    <div class="text-center text-sm text-slate-500">----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>

    <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Monthly transactions logged</h2>
                </div>
                <a href="{{ route('staff.transaction-log') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-500">Full log ↗</a>
            </div>
            <div class="mt-6 overflow-hidden rounded-3xl bg-slate-100 p-6">
                <div class="grid grid-cols-12 gap-3 text-[10px] uppercase tracking-[0.24em] text-slate-500">
                    <span class="col-span-4 text-left">Income entries</span>
                    <span class="col-span-4 text-right">_____________________</span>
                    <span class="col-span-4 text-center">Expense entries</span>
                </div>
                <div class="mt-6 space-y-3">
                    @php
                        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    @endphp
                    @foreach($months as $index => $month)
                        <div class="flex items-end gap-2">
                            <span class="w-8 text-xs text-slate-400">{{ $month }}</span>
                            <div class="flex-1 h-9 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-emerald-500" style="width: {{ min(100, ($monthlyIncome[$index] ?? 0) * 3) }}%"></div>
                            </div>
                            <div class="w-2 rounded-full bg-rose-500" style="height: {{ min(34, ($monthlyExpense[$index] ?? 0) * 1.5) }}px"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-950">Assigned clients</h2>
                    <a href="{{ route('staff.clients') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-500">View all ↗</a>
                </div>
                <div class="mt-6 space-y-3">
                    @forelse($assignedClients as $client)
                        <div class="flex items-center justify-between rounded-3xl bg-slate-50 p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500 text-sm font-semibold text-white">{{ strtoupper(substr($client->name, 0, 2)) }}</div>
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $client->clientProfile?->first_name ?? $client->name }}</p>
                                    <p class="text-sm text-slate-500">Personal · Since {{ $client->created_at?->format('M Y') ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                <span class="font-semibold text-slate-950">{{ $client->profileAccounts()->sum('balance') ? '₱' . number_format($client->profileAccounts()->sum('balance'), 0) : '₱0' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl bg-slate-50 p-6 text-center text-sm text-slate-500">No clients assigned yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </div>

</div>
</div>

<div class="text-center text-sm text-slate-500">For any questions or concerns, please contact support@fintrack.com</div>
@endsection
