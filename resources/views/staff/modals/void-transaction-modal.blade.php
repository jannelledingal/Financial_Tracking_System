@extends('layouts.staff')

@section('content')
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full max-h-screen overflow-y-auto">
        <h2 class="text-2xl font-bold text-rose-600 mb-2">Void Transaction</h2>
        <p class="text-sm text-slate-500 mb-6">This action will mark the transaction as voided and remove it from calculations.</p>

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
                <ul class="text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Transaction Details -->
        <div class="bg-slate-50 rounded-2xl p-4 mb-6 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Amount:</span>
                <span class="font-bold text-slate-900">₱{{ number_format(abs($transaction->amount), 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Category:</span>
                <span class="font-bold text-slate-900">{{ $transaction->category->category_name }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Type:</span>
                <span class="font-bold {{ $transaction->type === 'Income' ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $transaction->type }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Date:</span>
                <span class="font-bold text-slate-900">{{ $transaction->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <form action="{{ route('transactions.void', $transaction->id) }}" method="POST" class="space-y-4">
            @csrf

            <!-- Void Reason -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Reason for Voiding *</label>
                <textarea 
                    name="void_reason" 
                    rows="4"
                    required
                    placeholder="Explain why this transaction is being voided (e.g., duplicate entry, data entry error, unauthorized transaction)..."
                    class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500 @error('void_reason') border-red-500 @enderror"
                >{{ old('void_reason') }}</textarea>
            </div>

            <!-- Password Verification -->
            <div class="border-t border-slate-200 pt-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Confirm with Password *</label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    placeholder="Enter the transaction password"
                    class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500 @error('password') border-red-500 @enderror"
                >
                <p class="text-xs text-slate-500 mt-1">Password protection required for transaction modifications</p>
            </div>

            <!-- Warning -->
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4">
                <p class="text-xs text-rose-700">
                    ⚠️ <strong>Warning:</strong> This action cannot be undone. The transaction will be marked as voided and removed from all account calculations.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <a href="{{ route('staff.transaction-log') }}" class="flex-1 rounded-xl border-slate-300 px-6 py-3 text-sm font-semibold text-slate-950 border hover:bg-slate-50">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="flex-1 rounded-xl bg-rose-600 px-6 py-3 text-sm font-semibold text-white hover:bg-rose-700"
                >
                    Void Transaction
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
