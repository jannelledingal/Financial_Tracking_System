@extends('layouts.staff')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Add transaction</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Log new transaction</h1>
            <p class="mt-2 text-sm text-slate-500">Record income or expense for assigned client accounts.</p>
        </div>
    </div>

 

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <form method="POST" action="{{ route('staff.add-transaction.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="account_id" class="block text-sm font-medium text-slate-700">Client Account</label>
                    <select id="account_id" name="account_id" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->user->name ?? 'Client' }} - {{ $account->account_type }} (₱{{ number_format($account->balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700">Transaction Type</label>
                    <select id="type" name="type" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                        <option value="">Select Type</option>
                        <option value="Income">Income</option>
                        <option value="Expense">Expense</option>
                    </select>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="amount" class="block text-sm font-medium text-slate-700">Amount</label>
                    <div class="relative mt-2">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">₱</span>
                        <input type="number" step="0.01" name="amount" id="amount" required class="block w-full rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-3 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700">Category (Optional)</label>
                    <select id="category_id" name="category_id" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                <textarea id="description" name="description" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="Enter transaction description..."></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                    Submit Transaction
                </button>
                <button type="reset" class="inline-flex items-center justify-center rounded-full bg-slate-100 px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-200">
                    Reset
                </button>
            </div>
        </form>
    </div>



    {{-- Add account section --}}
    <div class="mt-8 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900">Add Client Account</h2>
            <p class="text-sm text-slate-500">Create a new financial account for an assigned client.</p>
        </div>

        <form action="{{ route('staff.accounts.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Select Client --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Choose Client</label>
                    <select name="client_id" required class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Account Type --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Account Type</label>
                    <input type="text" name="account_type" placeholder="e.g. Savings, Business, Personal" required 
                        class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Initial Balance --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Initial Balance</label>
                    <input type="number" name="balance" step="0.01" placeholder="0.00" required 
                        class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Currency --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Currency</label>
                    <select name="currency" required class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="PHP">PHP (Philippine Peso)</option>
                        <option value="USD">USD (US Dollar)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-blue-700 transition-all">
                    Add Account
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
