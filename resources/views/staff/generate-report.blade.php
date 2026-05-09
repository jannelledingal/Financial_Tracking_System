@extends('layouts.staff')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Reports</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Generate report</h1>
            <p class="mt-2 text-sm text-slate-500">Build a summary for your active client portfolio.</p>
        </div>
    </div>

    <!-- Client Selection Form -->
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <form action="{{ route('staff.reports') }}" method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label for="client_id" class="block text-sm font-medium text-slate-700 mb-2">Select Client</label>
                <select id="client_id" name="client_id" class="block w-full rounded-lg border border-slate-300 bg-gray-50 px-3 py-3 text-black focus:border-slate-500 focus:outline-none">
                    <option value="">Select a client</option>
                    @foreach($assignedClients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                Generate Report
            </button>
        </form>
    </div>

    @if($reportData)
    <!-- Financial Statement Report -->
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-slate-950">Financial Statement Report</h2>
            <span class="text-sm text-slate-500">{{ now()->format('F d, Y') }}</span>
        </div>
        
        <div class="border-b border-slate-200 pb-4 mb-4">
            <h3 class="text-lg font-semibold text-slate-900">{{ $reportData['client']->first_name }} {{ $reportData['client']->last_name }}</h3>
            <p class="text-sm text-slate-500">Client Report</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3 mb-6">
            <div class="p-4 bg-emerald-50 rounded-xl">
                <p class="text-sm text-emerald-600 font-medium">Total Income</p>
                <p class="text-2xl font-bold text-emerald-700">₱{{ number_format($reportData['totalIncome'], 2) }}</p>
            </div>
            <div class="p-4 bg-rose-50 rounded-xl">
                <p class="text-sm text-rose-600 font-medium">Total Expenses</p>
                <p class="text-2xl font-bold text-rose-700">₱{{ number_format($reportData['totalExpense'], 2) }}</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl">
                <p class="text-sm text-slate-600 font-medium">Net Balance</p>
                <p class="text-2xl font-bold text-slate-700">₱{{ number_format($reportData['netBalance'], 2) }}</p>
            </div>
        </div>

        <h4 class="font-semibold text-slate-900 mb-3">Recent Transactions</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Account</th>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($reportData['transactions'] as $trans)
                        <tr>
                            <td class="px-4 py-3">{{ $trans->created_at->format('M d, Y H:i A') }}</td>
                            <td class="px-4 py-3">{{ $trans->account->account_type ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $trans->category->category_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $trans->description ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $trans->type === 'Income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $trans->type === 'Income' ? '+' : '-' }}₱{{ number_format(abs($trans->amount), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Active clients</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $clientCount }}</p>
            <p class="mt-3 text-sm text-slate-500">Current portfolio size</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Reports this month</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $reportCount }}</p>
            <p class="mt-3 text-sm text-slate-500">Documents generated</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Pending approvals</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">2</p>
            <p class="mt-3 text-sm text-rose-500">Needs sign-off</p>
        </div>
    </div>

    
</div>
@endsection
