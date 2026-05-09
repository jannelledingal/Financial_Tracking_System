@extends('layouts.staff')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Edit Account</h1>
        <p class="text-sm text-slate-500">Update details for {{ $client->name ?? 'Client' }}'s {{ $account->account_type }} account.</p>
    </div>

    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
        <form action="{{ route('staff.accounts.update', $account->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Account Type</label>
                    <input type="text" name="account_type" value="{{ $account->account_type }}" required 
                           class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500">
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">Current Balance</label>
                    <input type="number" step="0.01" value="{{ $account->balance }}" disabled
                           class="w-full rounded-xl border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500">
                    <p class="mt-1 text-xs text-slate-400">Balance is updated automatically through transactions.</p>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">Currency</label>
                    <select name="currency" required class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500">
                        <option value="PHP" {{ $account->currency == 'PHP' ? 'selected' : '' }}>PHP (Philippine Peso)</option>
                        <option value="USD" {{ $account->currency == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4">
                <a href="{{ url()->previous() }}" class="text-sm text-slate-500 hover:underline">Cancel</a>
                <button type="submit" class="rounded-xl bg-slate-950 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-slate-800">
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection