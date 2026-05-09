@extends('layouts.staff')

@section('content')
@php
    // Dashboard logic variables
    $incomeValues = array_values($monthlyIncome);
    $expenseValues = array_values($monthlyExpense);
    $hasData = (array_sum($incomeValues) + array_sum($expenseValues)) > 0;
    $chartLabels = $labels ?? ['Jan','Feb','Mar', 'Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $savingsValues = [];
    foreach ($incomeValues as $key => $inc) {
        $savingsValues[] = $inc - ($expenseValues[$key] ?? 0);
    }
@endphp

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        {{-- Header Section with Period Filters --}}
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight uppercase">Client Analysis</h1>
                <p class="text-sm text-slate-500 mt-1">Reviewing financial status for {{ $client->name }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @php $currentPeriod = request('period', 'year'); @endphp

                <a href="{{ route('staff.show-client', [$client->id, 'period' => 'today']) }}" 
                   class="inline-flex items-center rounded-full border {{ $currentPeriod == 'today' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-2 text-xs font-bold transition hover:bg-slate-50">
                   Daily
                </a>

                <a href="{{ route('staff.show-client', [$client->id, 'period' => 'week']) }}" 
                   class="inline-flex items-center rounded-full border {{ $currentPeriod == 'week' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-2 text-xs font-bold transition hover:bg-slate-50">
                   Weekly
                </a>

                <a href="{{ route('staff.show-client', [$client->id, 'period' => 'month']) }}" 
                   class="inline-flex items-center rounded-full border {{ $currentPeriod == 'month' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-2 text-xs font-bold transition hover:bg-slate-50">
                   Monthly
                </a>

                <a href="{{ route('staff.show-client', [$client->id, 'period' => 'year']) }}" 
                   class="inline-flex items-center rounded-full border {{ $currentPeriod == 'year' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-2 text-xs font-bold transition hover:bg-slate-50">
                   Yearly
                </a>

                <a href="{{ route('staff.clients') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-full text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    ← Back
                </a>
            </div>
        </div>

        {{-- Top Summary Cards --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">Total Income</p>
                <p class="mt-4 text-3xl font-bold text-slate-950">₱{{ number_format($incomeTotal, 0) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">Total Expenses</p>
                <p class="mt-4 text-3xl font-bold text-slate-950">₱{{ number_format($expenseTotal, 0) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">Net Savings</p>
                <p class="mt-4 text-3xl font-bold text-emerald-600">₱{{ number_format(max($balanceTotal - $expenseTotal, 0), 0) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 border-l-4 border-indigo-600">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">Current Balance</p>
                <p class="mt-4 text-3xl font-bold text-slate-950">₱{{ number_format($balanceTotal, 0) }}</p>
            </div>
            
        </div>


        {{-- Financial Insights Date Picker --}}
        <div class="mb-6 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-slate-800">Financial Insights</h2>
                
                <form action="{{ route('staff.show-client', $client->id) }}" method="GET" class="flex items-center gap-3">
                    <input type="date" name="custom_date" value="{{ request('custom_date') }}" onchange="this.form.submit()"
                        class="rounded-xl border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                </form>
            </div>
        </div>


        {{-- Charts --}}
        <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
            {{-- Cash Flow Chart --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 flex flex-col">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Cash Flow — {{ request('period') ? ucfirst(request('period')) : 'Yearly' }}</h2>
                <div class="h-[300px] w-full">
                    <canvas id="cashFlowChart"></canvas>
                </div>
                <div class="mt-8 grid grid-cols-4 border-t border-slate-100 divide-x divide-slate-100">
                    <div class="pt-6 text-center">
                        <p class="text-xl font-black text-emerald-600">₱{{ $hasData ? number_format(array_sum($incomeValues) / 1000, 0) . 'K' : '0' }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Income</p>
                    </div>
                    <div class="pt-6 text-center">
                        <p class="text-xl font-black text-rose-600">₱{{ $hasData ? number_format(array_sum($expenseValues) / 1000, 0) . 'K' : '0' }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Expense</p>
                    </div>
                    <div class="pt-6 text-center">
                        <p class="text-xl font-black text-indigo-600">₱{{ $hasData ? number_format((array_sum($incomeValues) - array_sum($expenseValues)) / 1000, 0) . 'K' : '0' }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Savings</p>
                    </div>
                    <div class="pt-6 text-center">
                        <p class="text-xl font-black text-amber-500">{{ $hasData && array_sum($incomeValues) > 0 ? round((array_sum($incomeValues) - array_sum($expenseValues)) / array_sum($incomeValues) * 100) . '%' : '0%' }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Rate</p>
                    </div>
                </div>
            </div>

            {{-- Spending Donut --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 flex flex-col">
                <h2 class="text-lg font-bold text-slate-800 mb-8">Overall Spending by Category</h2>
                <div class="relative flex items-center justify-center min-h-[220px]">
                     <canvas id="spendingDonutChart"></canvas>
                </div>
                <div class="mt-8 space-y-4">
                    @foreach($spendingByCategory as $category)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                                <span class="text-sm font-semibold text-slate-600">{{ $category['name'] }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black text-slate-900">₱{{ number_format($category['amount'], 0) }}</span>
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ round($category['amount'] / max(1, $expenseTotal) * 100) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


        {{-- Client Accounts Section --}}
        <div class="mt-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Client Accounts</h3>
                    <p class="text-xs text-slate-500">Manage financial accounts for {{ $client->name }}</p>
                </div>
                <a href="{{ route('staff.add-transaction') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 transition-all">
                    + Add Account
                </a>
            </div>

            <div class="space-y-4">
                @forelse($accounts as $acc)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-slate-900">{{ $acc->account_type }}</h4>
                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold bg-slate-200 text-slate-700 rounded-full">ID: {{ $acc->id }}</span>
                                </div>
                                <p class="text-xs text-slate-500">{{ $acc->currency }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-900">
                                    {{ $acc->currency == 'PHP' ? '₱' : '$' }}{{ number_format($acc->balance, 2) }}
                                </p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Current Balance</p>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                {{-- Edit Button (Trigger Modal or Link) --}}
                                <button onclick="window.location.href='{{ route('staff.accounts.edit', $acc->id) }}'" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                
                                {{-- Delete Button --}}
                                <form action="{{ route('staff.accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-slate-500 py-4">No accounts found for this client.</p>
                @endforelse
            </div>
        </div>


        {{-- Recent Financial Activity Section --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 italic uppercase tracking-tight">Recent Financial Activity</h3>
                        <div class="flex flex-wrap gap-2 mt-2" id="transaction-filters">
                            <button onclick="filterTransactions('all', this)" class="filter-btn active-filter px-3 py-1 text-xs font-bold rounded-full border border-indigo-600 bg-indigo-600 text-white transition">All</button>
                            <button onclick="filterTransactions('today', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100 transition">Today</button>
                            <button onclick="filterTransactions('week', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100 transition">This Week</button>
                            <button onclick="filterTransactions('month', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100 transition">This Month</button>
                            <button onclick="filterTransactions('year', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100 transition">This Year</button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('transactions.export', $client->id) }}" class="inline-flex items-center gap-2 cursor-pointer rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-md">
                            📥 Export CSV
                        </a>
                        <form action="{{ route('transactions.import', $client->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <label class="inline-flex items-center gap-2 cursor-pointer rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-md">
                                📤 Import CSV
                                <input type="file" name="csv_file" accept=".csv,.txt" class="sr-only" onchange="this.form.submit()" />
                            </label>
                        </form>
                    </div>
                </div>

                {{-- Import History Section --}}
                @php
                    $allImports = \App\Models\TransactionImport::where('client_id', $client->id)
                        ->orderByDesc('created_at')
                        ->take(5)
                        ->get();
                @endphp

                @if($allImports->count() > 0)
                    <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h4 class="text-sm font-bold text-slate-800 mb-3">📋 Import History</h4>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($allImports as $import)
                                <div class="flex items-center justify-between bg-white rounded-lg p-3 text-xs">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900">{{ $import->import_data['file_name'] ?? 'Unknown' }}</p>
                                        <p class="text-slate-500">
                                            {{ $import->import_data['imported_count'] ?? 0 }} imported
                                            @if(($import->import_data['skipped_count'] ?? 0) > 0)
                                                • {{ $import->import_data['skipped_count'] }} skipped
                                            @endif
                                            • {{ $import->created_at->format('M d, Y H:i A') }}
                                            • Status: <span class="font-semibold {{ $import->status === 'completed' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst($import->status) }}</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($import->status === 'completed')
                                            <form action="{{ route('transactions.undo', [$client->id, $import->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs font-semibold bg-amber-100 text-amber-700 rounded hover:bg-amber-200 transition" title="Undo this import">↶ Undo</button>
                                            </form>
                                        @elseif($import->status === 'undone')
                                            <form action="{{ route('transactions.redo', [$client->id, $import->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition" title="Redo this import">↷ Redo</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('transactions.delete', [$client->id, $import->id]) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this import and all {{ count($import->transaction_ids ?? []) }} transactions? This cannot be undone.');">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-semibold bg-rose-100 text-rose-700 rounded hover:bg-rose-200 transition" title="Delete this import permanently">🗑️ Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center text-sm text-slate-500">
                        No imports yet. Upload a CSV file to get started.
                    </div>
                @endif

                <div class="text-xs text-slate-500 mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <strong>📌 CSV Format Guide:</strong> Use <strong>account_id</strong> (see IDs above) OR <strong>account_type</strong> to match accounts. Other headers: <strong>category_id</strong> or <strong>category</strong>, <strong>type</strong> (Income/Expense), <strong>amount</strong>, <strong>date</strong>, <strong>description</strong>.
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="transactions-table">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="p-4">Date & Time</th>
                                <th class="p-4">Type</th>
                                <th class="p-4">Description</th>
                                <th class="p-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transactions as $trans)
                            <tr class="transaction-row" data-timestamp="{{ $trans->created_at->timestamp }}">
                                <td class="p-4 text-sm font-medium">{{ $trans->created_at->format('M d, Y H:i A') }}</td>
                                @php
                                    $isIncome = strtolower($trans->type) === 'income';
                                    $label = $isIncome ? 'Deposit' : 'Withdrawal';
                                @endphp
                                <td class="p-4 uppercase text-[10px] font-black">
                                     <span class="px-2 py-1 rounded {{ $isIncome ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $label }}
                                     </span>
                                </td>
                                <td class="p-4 text-sm text-gray-600">{{ $trans->description }}</td>
                                <td class="p-4 text-right font-bold {{ $isIncome ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $isIncome ? '+' : '-' }}₱{{ number_format(abs($trans->amount), 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-gray-400">No transactions recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

    </div>

    
</div>

{{-- Charts Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @php
        $catNames = collect($spendingByCategory)->pluck('name')->toArray();
        $catAmounts = collect($spendingByCategory)->pluck('amount')->toArray();
    @endphp

    function initCharts() {
        const ctxLine = document.getElementById('cashFlowChart').getContext('2d');
        const ctxDonut = document.getElementById('spendingDonutChart').getContext('2d');
        const hasData = @json($hasData);

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    { label: 'Income', data: @json($incomeValues), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.05)', fill: true, tension: 0.4, hidden: !hasData },
                    { label: 'Expenses', data: @json($expenseValues), borderColor: '#f43f5e', backgroundColor: 'rgba(244, 63, 94, 0.05)', fill: true, tension: 0.4, hidden: !hasData },
                    { label: 'Savings', data: @json($savingsValues), borderColor: '#6366f1', borderDash: [5, 5], fill: false, tension: 0.4, hidden: !hasData }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: @json($catNames),
                datasets: [{
                    data: @json($catAmounts),
                    backgroundColor: ['#f43f5e', '#3b82f6', '#b45309', '#8b5cf6', '#10b981'],
                    cutout: '70%',
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
    initCharts();
</script>


<script>
function filterTransactions(range, buttonElement) {
    const rows = document.querySelectorAll('.transaction-row');
    const now = Math.floor(Date.now() / 1000);
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white', 'active-filter');
        btn.classList.add('text-slate-600', 'bg-transparent', 'border-slate-200');
    });
    
    if (buttonElement) {
        buttonElement.classList.add('bg-indigo-600', 'text-white', 'active-filter');
        buttonElement.classList.remove('text-slate-600', 'bg-transparent', 'border-slate-200');
    }

    rows.forEach(row => {
        const timestamp = parseInt(row.getAttribute('data-timestamp'));
        let show = false;

        if (range === 'all') {
            show = true;
        } else if (range === 'today') {
            const today = new Date().setHours(0,0,0,0) / 1000;
            show = timestamp >= today;
        } else if (range === 'week') {
            const weekAgo = now - (7 * 24 * 60 * 60);
            show = timestamp >= weekAgo;
        } else if (range === 'month') {
            const monthAgo = now - (30 * 24 * 60 * 60);
            show = timestamp >= monthAgo;
        } else if (range === 'year') {
            const yearAgo = now - (365 * 24 * 60 * 60);
            show = timestamp >= yearAgo;
        }

        row.style.display = show ? '' : 'none';
    });
}

// Initialize on page load - show all transactions
document.addEventListener('DOMContentLoaded', function() {
    const allButton = document.querySelector('button[onclick*="filterTransactions(\'all\')"]');
    if (allButton) {
        filterTransactions('all', allButton);
    }
});
</script>
@endsection