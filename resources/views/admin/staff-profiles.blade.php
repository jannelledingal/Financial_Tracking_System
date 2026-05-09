@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-950">Staff Profiles</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-500">View and manage staff user profiles, roles, and assignments.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Add staff</a>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-950">Active staff members</h2>
            <span class="rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600">6 total</span>
        </div>

        <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Name</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Email</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Role</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Status</th>
                        <th class="px-6 py-4 text-center font-semibold uppercase tracking-[0.16em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-slate-600">
                    @forelse($staffMembers as $staff)
                        <tr>
                            <td class="px-6 py-5 font-semibold text-slate-950">{{ $staff->name }}</td>
                            <td class="px-6 py-5">{{ $staff->email }}</td>
                            <td class="px-6 py-5">{{ $staff->role }}</td>
                            <td class="px-6 py-5">
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $staff->account_status === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $staff->account_status }}</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <a href="{{ route('admin.users.show', $staff) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No staff members found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
