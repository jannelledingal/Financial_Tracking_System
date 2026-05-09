@extends('layouts.staff')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Income records</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Income entries</h1>
            <p class="mt-2 text-sm text-slate-500">Track all income recorded for your assigned clients.</p>
        </div>
        <div class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Total ₱{{ number_format($totalIncome, 2) }}</div>
    </div>

    <!-- Period Filter -->
    <div class="flex gap-2">
        <a href="{{ route('staff.income-records', ['period' => 'day']) }}" class="rounded-full px-4 py-2 text-sm font-medium {{ $period === 'day' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Day</a>
        <a href="{{ route('staff.income-records', ['period' => 'week']) }}" class="rounded-full px-4 py-2 text-sm font-medium {{ $period === 'week' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Week</a>
        <a href="{{ route('staff.income-records', ['period' => 'month']) }}" class="rounded-full px-4 py-2 text-sm font-medium {{ $period === 'month' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Month</a>
        <a href="{{ route('staff.income-records', ['period' => 'year']) }}" class="rounded-full px-4 py-2 text-sm font-medium {{ $period === 'year' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Year</a>
    </div>

    <!-- Chart -->
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <h3 class="text-lg font-semibold text-slate-950 mb-4">Income Analysis</h3>
        <div class="h-64">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Date</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Client</th> 
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Account</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Category</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Description</th>
                        <th class="px-6 py-4 text-right font-semibold uppercase tracking-[0.14em]">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-slate-600">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-5">{{ $transaction->created_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-5">{{ $transaction->account->user->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-5">{{ $transaction->account->account_type ?? 'Account' }}</td>
                            <td class="px-6 py-5">{{ $transaction->category->category_name ?? 'Unknown' }}</td>
                            <td class="px-6 py-5">{{ $transaction->description ?? '—' }}</td>
                            <td class="px-6 py-5 text-right font-semibold text-emerald-600">+₱{{ number_format($transaction->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No income records available.</td>
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


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Income',
                data: @json($chartValues),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>

@endsection
