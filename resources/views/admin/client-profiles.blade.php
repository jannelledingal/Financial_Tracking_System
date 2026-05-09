@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-950">Client Profiles</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-500">Browse active clients, contact details, and current account status.</p>
        </div>
        
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-950">All client accounts</h2>
            <span class="rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600">{{ $clients->count() }} clients</span>
        </div>

        <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Client</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Email</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Status</th>
                        <th class="px-6 py-4 text-center font-semibold uppercase tracking-[0.16em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-slate-600">
                    @forelse($clients as $client)
                        <tr>
                            <td class="px-6 py-5 font-semibold text-slate-950">{{ $client->name }}</td>
                            <td class="px-6 py-5">{{ $client->email }}</td>
                            <td class="px-6 py-5">
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $client->account_status === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $client->account_status }}</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <a href="{{ route('admin.users.show', $client) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">No clients found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
