@extends('layouts.client')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Transaction history</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">All activity</h1>
            <p class="mt-2 text-sm text-slate-500">Review every transaction across your accounts.</p>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700">Showing {{ $transactions->count() }}</div>
    </div>

    <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200/70">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Date & Time</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Account</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Category</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Type</th>
                    <th class="px-6 py-4 text-right font-semibold uppercase tracking-[0.14em]">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white text-slate-700">
                @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4">{{ $transaction->created_at->format('M d, Y H:i A') }}</td>
                        <td class="px-6 py-4">{{ $transaction->account->account_type ?? 'Account' }}</td>
                        <td class="px-6 py-4">{{ $transaction->category?->category_name ?? 'Other' }}</td>
                        <td class="px-6 py-4">{{ $transaction->type }}</td>
                        <td class="px-6 py-4 text-right font-semibold {{ $transaction->type === 'Income' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $transaction->type === 'Income' ? '+' : '-' }}₱{{ number_format(abs($transaction->amount), 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">No transactions available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between text-sm text-slate-500">
        <span>Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</span>
        {{ $transactions->links() }}
    </div>
</div>
@endsection
