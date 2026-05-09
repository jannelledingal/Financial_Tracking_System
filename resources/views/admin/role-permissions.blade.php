@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-semibold text-slate-950">Role & Permissions</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-500">Permission levels and access for Administrator, Staff, and Client roles INFO</p>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-950">Permission matrix</h2>
        </div>

         <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Permission</th>
                            <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Admin</th>
                            <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Staff</th>
                            <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.14em]">Client</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white text-slate-600">
                        @php
                            $permissions = [
                                ['label' => 'View own dashboard', 'admin' => 'full', 'staff' => 'full', 'client' => 'full'],
                                ['label' => 'Log transactions', 'admin' => 'full', 'staff' => 'full', 'client' => 'none'],
                                ['label' => 'View all clients', 'admin' => 'full', 'staff' => 'assigned', 'client' => 'none'],
                                ['label' => 'Generate reports', 'admin' => 'full', 'staff' => 'full', 'client' => 'none'],
                                ['label' => 'Send messages', 'admin' => 'none', 'staff' => 'full', 'client' => 'full'],
                                ['label' => 'Manage users', 'admin' => 'full', 'staff' => 'none', 'client' => 'none'],
                                ['label' => 'Assign staff to clients', 'admin' => 'full', 'staff' => 'none', 'client' => 'none'],
                                ['label' => 'Delete records', 'admin' => 'full', 'staff' => 'none', 'client' => 'none'],
                                ['label' => 'System settings', 'admin' => 'full', 'staff' => 'none', 'client' => 'none'],
                            ];
                            $badgeClasses = [
                                'full' => 'bg-violet-100 text-violet-700',
                                'assigned' => 'bg-amber-100 text-amber-700',
                                'none' => 'bg-slate-100 text-slate-500',
                            ];
                            $badgeLabels = [
                                'full' => 'Full access',
                                'assigned' => 'Assigned only',
                                'none' => 'No access',
                            ];
                        @endphp

                        @foreach ($permissions as $permission)
                            <tr>
                                <td class="px-6 py-4">{{ $permission['label'] }}</td>
                                <td class="px-6 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses[$permission['admin']] }}">{{ $badgeLabels[$permission['admin']] }}</span></td>
                                <td class="px-6 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses[$permission['staff']] }}">{{ $badgeLabels[$permission['staff']] }}</span></td>
                                <td class="px-6 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses[$permission['client']] }}">{{ $badgeLabels[$permission['client']] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
</div>
@endsection
