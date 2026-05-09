@extends('layouts.client')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Accounts</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">My accounts</h1>
            <p class="mt-2 text-sm text-slate-500">Review balances and account activity in one place.</p>
        </div>
        <a href="{{ route('client.income') }}" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">View Income</a>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        @forelse($accounts as $account)
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.24em] text-slate-500">{{ $account->account_type ?? 'Account' }}</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-950">{{ $account->account_name }}</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700">{{ $account->transactions_count }} entries</span>
                </div>
                <div class="mt-6 border-t border-slate-200 pt-5">
                    <p class="text-sm text-slate-500">Current balance</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">₱{{ number_format($account->balance, 0) }}</p>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white p-12 text-center text-slate-500">No accounts found for this client.</div>
        @endforelse
    </div>
</div>
@endsection
