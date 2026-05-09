@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-semibold text-slate-950">Staff Assignments</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-500">Assign staff to client accounts and review relationship load at a glance.</p>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-950">Assignment overview</h2>
            <button onclick="document.getElementById('assign-modal').classList.remove('hidden')" class="text-sm font-semibold text-sky-600 hover:text-sky-500">Manage assignments ↗</button>
        </div>

        <div class="mt-6 grid gap-4">
            {{-- $staffProfileId is the ID from the staff_profiles table --}}
            @forelse($assignments as $staffProfileId => $staffAssignments)
                @php
                    // Retrieve the staff user through the first assignment's profile relationship
                    $firstAssignment = $staffAssignments->first();
                    $staffUser = $firstAssignment->staffProfile->user ?? null;
                    
                    $count = $staffAssignments->count();
                    $badgeColor = $count > 5 ? 'bg-rose-100 text-rose-700' : ($count > 2 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                @endphp
                <div class="rounded-3xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            {{-- Correctly displays the staff member's name --}}
                            <p class="text-base font-semibold text-slate-950">{{ $staffUser?->name ?? 'Unknown Staff' }}</p>
                            <p class="text-sm text-slate-500">{{ $count }} assigned clients</p>
                        </div>
                        <span class="inline-flex rounded-full px-3 py-2 text-sm font-semibold {{ $badgeColor }}">{{ $count }} clients</span>
                    </div>

                    <div class="space-y-2">
                        @foreach($staffAssignments as $assignment)
                            <div class="flex items-center justify-between bg-white rounded-2xl p-3">
                                <div class="flex items-center gap-3">
                                    {{-- Correctly resolve client name through the clientProfile relationship --}}
                                    @php 
                                        $clientName = $assignment->clientProfile->user->name ?? 'Unknown Client'; 
                                    @endphp
                                    
                                    <div class="flex h-8 w-8 items-center justify-center rounded-2xl bg-slate-100 text-xs font-semibold text-slate-700">
                                        {{ strtoupper(substr($clientName, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-950">{{ $clientName }}</p>
                                        <p class="text-xs text-slate-500">Assigned {{ $assignment->assigned_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('admin.staff-assignments.destroy', $assignment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-500" onclick="return confirm('Remove this assignment?')">Remove</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-3xl bg-slate-50 p-8 text-center">
                    <p class="text-slate-500">No staff assignments yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <div id="assign-modal" class="fixed inset-0 z-50 hidden bg-slate-900/50">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-slate-950">Assign Staff to Client</h3>
                    <button onclick="document.getElementById('assign-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form action="{{ route('admin.staff-assignments.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Select Staff Member</label>
                        <select name="staff_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200" required>
                            <option value="">Choose staff...</option>
                            @foreach(\App\Models\User::where('role', 'Staff')->get() as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Select Client</label>
                        <select name="client_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200" required>
                            <option value="">Choose client...</option>
                            @foreach(\App\Models\User::where('role', 'Client')->get() as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('assign-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-2xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-slate-950 hover:bg-slate-800 rounded-2xl">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection