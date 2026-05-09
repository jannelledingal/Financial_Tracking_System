@extends('layouts.staff')

@section('content')
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full max-h-screen overflow-y-auto">
        <h2 class="text-2xl font-bold text-slate-950 mb-6">Edit Transaction</h2>

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

        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <!-- Original Amount Display -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Original Amount</label>
                <div class="text-lg font-bold text-slate-900">₱{{ number_format($transaction->amount, 2) }}</div>
            </div>

            <!-- New Amount -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">New Amount</label>
                <input 
                    type="number" 
                    name="amount" 
                    step="0.01" 
                    value="{{ old('amount', $transaction->amount) }}" 
                    required
                    class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500 @error('amount') border-red-500 @enderror"
                >
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                <select name="category_id" required class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500 @error('category_id') border-red-500 @enderror">
                    <option value="">Select a category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                <textarea 
                    name="description" 
                    rows="2"
                    class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500 @error('description') border-red-500 @enderror"
                >{{ old('description', $transaction->description) }}</textarea>
            </div>

            <!-- Edit Reason -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Edit Reason *</label>
                <textarea 
                    name="edit_reason" 
                    rows="3"
                    required
                    placeholder="Explain why this transaction needs to be corrected..."
                    class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500 @error('edit_reason') border-red-500 @enderror"
                >{{ old('edit_reason') }}</textarea>
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

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <a href="{{ route('staff.transaction-log') }}" class="flex-1 rounded-xl border-slate-300 px-6 py-3 text-sm font-semibold text-slate-950 border hover:bg-slate-50">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="flex-1 rounded-xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white hover:bg-sky-700"
                >
                    Update Transaction
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
