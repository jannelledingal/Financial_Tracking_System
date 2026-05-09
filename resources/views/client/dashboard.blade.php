@extends('layouts.client')

@section('content')
@php
    // Define $hasData at the top so it's available throughout the page
    $incomeValues = array_values($monthlyIncome);
    $expenseValues = array_values($monthlyExpense);
    $hasData = (array_sum($incomeValues) + array_sum($expenseValues)) > 0;
    $chartLabels = $labels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $savingsValues = [];
    foreach ($incomeValues as $key => $inc) {
        $savingsValues[] = $inc - ($expenseValues[$key] ?? 0);
    }
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            
            <h1 class="mt-3 text-3xl font-semibold text-slate-950">Good Day, {{ $client->clientProfile?->first_name ?? $client->name }}</h1>
            <p class="mt-2 text-sm text-slate-500">Here’s your financial status for {{ now()->format('F Y') }}.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @php 
                $currentPeriod = request('period', 'year'); 
            @endphp

            {{-- Daily Button --}}
            <a href="{{ route('client.dashboard', ['period' => 'today']) }}" 
               class="inline-flex items-center rounded-full border {{ $currentPeriod == 'today' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-3 text-sm font-semibold transition hover:bg-slate-50 hover:text-slate-900">
               Daily
            </a>

            {{-- Weekly Button --}}
            <a href="{{ route('client.dashboard', ['period' => 'week']) }}" 
               class="inline-flex items-center rounded-full border {{ $currentPeriod == 'week' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-3 text-sm font-semibold transition hover:bg-slate-50 hover:text-slate-900">
               Weekly
            </a>

            {{-- Monthly Button --}}
            <a href="{{ route('client.dashboard', ['period' => 'month']) }}" 
               class="inline-flex items-center rounded-full border {{ $currentPeriod == 'month' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-3 text-sm font-semibold transition hover:bg-slate-50 hover:text-slate-900">
               Monthly
            </a>

            {{-- Yearly Button --}}
            <a href="{{ route('client.dashboard', ['period' => 'year']) }}" 
               class="inline-flex items-center rounded-full border {{ ($currentPeriod == 'year' || !$currentPeriod) ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }} px-4 py-3 text-sm font-semibold transition hover:bg-slate-50 hover:text-slate-900">
               Yearly
            </a>

            <a href="{{ route('client.messages') }}" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                Ask your consultant
            </a>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-4">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Total income</p>
            <p class="mt-4 text-3xl font-semibold text-slate-950">₱{{ number_format($incomeTotal, 0) }}</p>
            <p class="mt-3 text-sm text-emerald-600">+9.2% vs last month</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Total expenses</p>
            <p class="mt-4 text-3xl font-semibold text-slate-950">₱{{ number_format($expenseTotal, 0) }}</p>
            <p class="mt-3 text-sm text-rose-600">+3.1% vs last month</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Net savings</p>
            <p class="mt-4 text-3xl font-semibold text-slate-950">₱{{ number_format(max($balanceTotal - $expenseTotal, 0), 0) }}</p>
            <p class="mt-3 text-sm text-emerald-600">+14.6% vs last month</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Overall balance</p>
            <p class="mt-4 text-3xl font-semibold text-slate-950">₱{{ number_format($balanceTotal, 0) }}</p>
            <p class="mt-3 text-sm text-slate-600">Current account balance</p>
        </div>
    </div>



<div class="mb-6 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-bold text-slate-800">Financial Insights</h2>
        
        <form action="{{ route('client.dashboard') }}" method="GET" class="flex items-center gap-3">

            {{-- Date Picker --}}
            <input type="date" name="custom_date" value="{{ request('custom_date') }}" onchange="this.form.submit()"
                class="rounded-xl border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
        </form>
    </div>
</div>


<div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
    {{-- LEFT COLUMN: CASH FLOW LINE GRAPH --}}
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-800">Cash flow — {{ $chartTitle ?? now()->year }}</h2>
            <a href="{{ route('client.transaction-history') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">Full report ↗</a>
        </div>

        {{-- Smooth Line Chart Container --}}
        <div class="h-[300px] w-full">
            <canvas id="cashFlowChart"></canvas>
        </div>

        {{-- BIG STATS SUMMARY: Large, colored typography --}}
        <div class="mt-8 grid grid-cols-4 border-t border-slate-100">
            <div class="pt-6 text-center border-r border-slate-100">
                <p class="text-2xl font-black text-emerald-600">₱{{ $hasData ? number_format(array_sum($incomeValues) / 1000, 0) . 'K' : '0' }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Income</p>
            </div>
            <div class="pt-6 text-center border-r border-slate-100">
                <p class="text-2xl font-black text-rose-600">₱{{ $hasData ? number_format(array_sum($expenseValues) / 1000, 0) . 'K' : '0' }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Expense</p>
            </div>
            <div class="pt-6 text-center border-r border-slate-100">
                <p class="text-2xl font-black text-indigo-600">₱{{ $hasData ? number_format((array_sum($incomeValues) - array_sum($expenseValues)) / 1000, 0) . 'K' : '0' }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Savings</p>
            </div>
            <div class="pt-6 text-center">
                <p class="text-2xl font-black text-amber-500">{{ $hasData && array_sum($incomeValues) > 0 ? round((array_sum($incomeValues) - array_sum($expenseValues)) / array_sum($incomeValues) * 100) . '%' : '0%' }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Save Rate</p>
            </div>
        </div>
    </div>


            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <h2 class="text-lg font-semibold text-slate-950">Recent transactions</h2>
                <div class="mt-6 space-y-4">
                    @forelse($recentTransactions as $transaction)
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $transaction->description ?? $transaction->category?->category_name ?? 'Transaction' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $transaction->created_at->format('M d, Y') }} · {{ $transaction->account->account_name ?? 'Account' }}</p>
                                </div>
                                <p class="font-semibold {{ $transaction->type === 'Income' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $transaction->type === 'Income' ? '+' : '-' }}₱{{ number_format(abs($transaction->amount), 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl bg-slate-50 p-6 text-center text-sm text-slate-500">No recent transactions found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
 </div>
<p class="mt-4 leading-relaxed text-slate-300 text-lg">
                                Contact jjannellev@gmail.com for any system concerns.


 {{-- RIGHT COLUMN: SPENDING DONUT --}}
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 flex flex-col">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-lg font-bold text-slate-800">Overall Spending by Category</h2>
    </div>
    <a href="{{ route('client.expenses') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">Details ↗</a>
    
    {{-- Chart Canvas --}}
    <div class="relative flex items-center justify-center min-h-[220px]">
         <canvas id="spendingDonutChart"></canvas>
    </div>

    {{-- Dynamic Legend --}}
    <div class="mt-8 space-y-4">
        @foreach($spendingByCategory as $category)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- Use dynamic colors from your data if available --}}
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


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Variables are already defined at the top of the file
    // Spending Categories
    @php
        $catNames = collect($spendingByCategory)->pluck('name')->toArray();
        $catAmounts = collect($spendingByCategory)->pluck('amount')->toArray();
    @endphp

    let cashFlowChart, spendingDonutChart;

    function initCharts() {
        const ctxLine = document.getElementById('cashFlowChart').getContext('2d');
        const ctxDonut = document.getElementById('spendingDonutChart').getContext('2d');
        const hasData = @json($hasData);

        if (cashFlowChart) cashFlowChart.destroy();
        if (spendingDonutChart) spendingDonutChart.destroy();

        // 2. PASS CLEAN VARIABLES TO JS
        cashFlowChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    { label: 'Income', data: @json($incomeValues), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.05)', fill: true, tension: 0.4, hidden: !hasData },
                    { label: 'Expenses', data: @json($expenseValues), borderColor: '#f43f5e', backgroundColor: 'rgba(244, 63, 94, 0.05)', fill: true, tension: 0.4, hidden: !hasData },
                    { label: 'Savings', data: @json($savingsValues), borderColor: '#6366f1', borderDash: [5, 5], fill: false, tension: 0.4, hidden: !hasData }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    title: {
                        display: !hasData,
                        text: 'No records found for this period',
                        color: '#94a3b8',
                        font: { size: 16 }
                    }
                },
                scales: {
                    y: { display: hasData },
                    x: { display: hasData }
                }
            }
        });

        spendingDonutChart = new Chart(ctxDonut, {
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
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // Run on load
    initCharts();
</script>


@endsection
