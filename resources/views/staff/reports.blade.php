@extends('layouts.staff')

@section('content')
<div class="space-y-6">
    {{-- Header & Back Button --}}
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between no-print">
        <div>
            <a href="{{ route('staff.reports') }}" class="group flex items-center text-slate-500 hover:text-slate-950 transition-colors mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7" />
                </svg>
                <span class="text-xs uppercase tracking-[0.24em] ml-1">Back</span>
            </a>
            <h1 class="text-3xl font-semibold text-slate-950">Generate Report</h1>
            <p class="mt-2 text-sm text-slate-500">Filter and export financial summaries for your active portfolio.</p>
        </div>
    </div>

    {{-- Selection Form --}}
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 no-print">
        <form action="{{ route('staff.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Select Client</label>
                <select name="client_id" required class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500">
                    <option value="">Choose a client...</option>
                    @foreach($assignedClients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Report Duration</label>
                <select name="period" class="block w-full rounded-xl border-slate-300 bg-gray-50 px-3 py-3 text-sm focus:ring-slate-500">
                    <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Report of the Day</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Report of the Week</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Report of the Month</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Report of the Year</option>
                    <option value="overall" {{ request('period') == 'overall' || !request('period') ? 'selected' : '' }}>Overall Report</option>
                </select>
            </div>

            <button type="submit" class="rounded-xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
                Generate Report
            </button>
        </form>
    </div>

    @if($reportData)
    {{-- Financial Statement Area --}}
    <div id="printableArea" class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-950">Financial Statement</h2>
                <p class="text-sm font-bold text-sky-600 uppercase tracking-widest mt-1">{{ $reportData['periodLabel'] }}</p>
            </div>
            <button onclick="window.print()" class="no-print flex items-center gap-2 rounded-full bg-sky-50 px-5 py-2 text-xs font-bold text-sky-700 hover:bg-sky-100 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                (PRINT) EXPORT AS PDF
            </button>
            <a href="{{ request()->fullUrlWithQuery(['download' => 1]) }}" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                DOWNLOAD CSV
            </a>
        </div>
        


        <div class="flex items-center gap-3 no-print">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">👤</span>
        </div>
        <div class="border-b border-slate-100 my-6"></div>

        <div class="border-b border-slate-100 pb-6 mb-6">
            <h3 class="text-lg font-semibold text-slate-900">{{ $reportData['client']->name }}</h3>
            <p class="text-sm text-slate-500">Generated on {{ now()->format('M d, Y H:i A') }} by {{ Auth::user()->name }}</p>
        </div>

       {{-- Summary Cards --}}
    <div class="grid gap-4 md:grid-cols-3 mb-8">
        <div class="p-5 bg-emerald-50 rounded-2xl border border-emerald-100 text-emerald-700">
            <p class="text-xs font-bold uppercase">Total Income</p>
            <p class="text-2xl font-bold">₱{{ number_format($reportData['totalIncome'], 2) }}</p>
        </div>
        <div class="p-5 bg-rose-50 rounded-2xl border border-rose-100 text-rose-700">
            <p class="text-xs font-bold uppercase">Total Expenses</p>
            <p class="text-2xl font-bold">₱{{ number_format($reportData['totalExpense'], 2) }}</p>
        </div>
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-slate-700">
            <p class="text-xs font-bold uppercase">Net Balance</p>
            <p class="text-2xl font-bold">₱{{ number_format($reportData['netBalance'], 2) }}</p>
        </div>
    </div>

        {{-- Table --}}
        <h4 class="font-bold text-slate-950 mb-4">Detailed Transaction History</h4>
        <div class="overflow-hidden rounded-2xl border border-slate-100">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-4 text-left font-bold text-slate-500 uppercase tracking-widest">Date</th>
                        <th class="px-4 py-4 text-left font-bold text-slate-500 uppercase tracking-widest">Account</th>
                        <th class="px-4 py-4 text-left font-bold text-slate-500 uppercase tracking-widest">Category</th>
                        <th class="px-4 py-4 text-right font-bold text-slate-500 uppercase tracking-widest">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reportData['transactions'] as $trans)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-4 text-slate-600">{{ $trans->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ $trans->account->account_type }}</td>
                            <td class="px-4 py-4 font-medium text-slate-900">{{ $trans->category->category_name }}</td>
                            <td class="px-4 py-4 text-right font-bold {{ $trans->type === 'Income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $trans->type === 'Income' ? '+' : '-' }}₱{{ number_format(abs($trans->amount), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-slate-400 italic">No transactions found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Print Optimization --}}
<style>
    @media print {
        .no-print, aside, nav, .back-btn { display: none !important; }
        body { background: white !important; padding: 0 !important; }
        #printableArea { 
            box-shadow: none !important; 
            ring: none !important; 
            border: none !important; 
            width: 100% !important;
            padding: 0 !important;
        }
        main { margin: 0 !important; padding: 0 !important; }
    }
</style>
@endsection