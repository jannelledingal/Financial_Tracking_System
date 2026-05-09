@extends('layouts.staff')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Client worksheet</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Assigned clients</h1>
            <p class="mt-2 text-sm text-slate-500">Manage your current client relationships and account summaries.</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Client</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Status</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Since</th>
                        <th class="px-6 py-4 text-right font-semibold uppercase tracking-[0.14em]">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-slate-600">
                    @forelse($clients as $client)
                        <tr>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-3xl bg-slate-100 text-sm font-semibold text-slate-800">{{ strtoupper(substr($client->name, 0, 2)) }}</div>
                                        <div>
                                            <p class="font-semibold text-slate-950">{{ $client->clientProfile?->first_name ?? $client->name }}</p>
                                            <p class="text-sm text-slate-500">{{ $client->email }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('staff.show-client', $client) }}" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-sky-700 transition">View Records</a>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Active</span>
                            </td>
                            <td class="px-6 py-5">{{ $client->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-5 text-right font-semibold text-slate-950">₱{{ number_format($client->profileAccounts()->sum('balance'), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">No clients assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
