@extends('layouts.staff')

@section('content')
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-slate-950 mb-2">Sync Account Balance</h2>
        <p class="text-sm text-slate-500 mb-6">Recalculate the account balance based on all transactions.</p>

        @if (session('success'))
            <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                ✓ {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('transactions.sync') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Account Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Select Account to Sync</label>
                <select name="account_id" required class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500">
                    <option value="">Choose an account...</option>
                    @foreach($accounts ?? [] as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->account_type }} - Current Balance: ₱{{ number_format($account->balance, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                <p class="text-xs text-blue-700">
                    ℹ️ This will recalculate the account balance based on all active (non-voided) transactions. Voided transactions will be excluded from the calculation.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <a href="{{ route('staff.transaction-log') }}" class="flex-1 rounded-xl border-slate-300 px-6 py-3 text-sm font-semibold text-slate-950 border hover:bg-slate-50">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="flex-1 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Sync Now
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
