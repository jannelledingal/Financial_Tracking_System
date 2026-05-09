@extends('layouts.staff')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Transaction log</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">All transactions</h1>
            <p class="mt-2 text-sm text-slate-500">Review entries across assigned client accounts.</p>
        </div>
        <div class="flex flex-wrap gap-3 items-center">
            <form action="{{ route('transactions.sync-all') }}" method="POST" class="inline-flex">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-amber-600">
                    Sync All
                </button>
            </form>
            <a href="{{ route('staff.add-transaction') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Create Transaction</a>
        </div>
    </div>

 

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Created At</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Updated At</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Client</th> 
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Account</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Category</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Type</th>
                        <th class="px-6 py-4 text-right font-semibold uppercase tracking-[0.14em]">Amount</th>
                        <th class="px-6 py-4 text-center font-semibold uppercase tracking-[0.14em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-slate-600">
                    @forelse($transactions as $transaction)
                        <tr class="{{ $transaction->isVoided() ? 'bg-red-50 opacity-60' : 'hover:bg-slate-50' }}">
                            <td class="px-6 py-5">
                                {{ $transaction->created_at?->format('M d, Y H:i A') }}
                            </td>
                            <td class="px-6 py-5">
                                {{ $transaction->updated_at?->format('M d, Y H:i A') }}
                            </td>
                            
                            {{-- Updated to use the 'user' relationship --}}
                            <td class="px-6 py-5 font-bold text-slate-900">
                                {{ $transaction->account->user->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-5">{{ $transaction->account->account_type ?? 'Account' }}</td>
                            <td class="px-6 py-5">{{ $transaction->category->category_name ?? 'Unknown' }}</td>
                            <td class="px-6 py-5">{{ $transaction->type }}</td>
                            <td class="px-6 py-5 text-right font-semibold {{ $transaction->type === 'Income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $transaction->type === 'Income' ? '+' : '-' }}₱{{ number_format(abs($transaction->amount), 2) }}
                                @if ($transaction->isEdited())
                                    <div class="text-xs text-slate-500">(edited from ₱{{ number_format($transaction->original_amount, 2) }})</div>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if (!$transaction->isVoided())
                                        <a href="{{ route('transactions.edit', $transaction->id) }}" 
                                           class="inline-flex items-center gap-1 rounded-lg bg-sky-100 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-200 transition-all"
                                           title="Edit this transaction">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <a href="{{ route('transactions.void.form', $transaction->id) }}" 
                                           class="inline-flex items-center gap-1 rounded-lg bg-rose-100 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-200 transition-all"
                                           title="Void this transaction">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Void
                                        </a>
                                        <form action="{{ route('transactions.sync') }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="account_id" value="{{ $transaction->account_id }}">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-200 transition-all" title="Sync this account balance">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 1112 0M19 5a9 9 0 10-12 0" />
                                                </svg>
                                                Sync
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs font-semibold text-rose-600 bg-rose-100 px-3 py-2 rounded-lg">VOIDED</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">No transactions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
