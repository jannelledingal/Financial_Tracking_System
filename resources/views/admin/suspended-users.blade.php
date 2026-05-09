@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-semibold text-slate-950">Suspended Users</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-500">Review suspended accounts and restore access when appropriate.</p>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-950">Accounts on hold</h2>
            <span class="rounded-full bg-amber-100 px-3 py-2 text-sm font-semibold text-amber-700">{{ $suspendedUsers->count() }} suspended</span>
        </div>

        <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">User</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Role</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Email</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Since</th>
                        <th class="px-6 py-4 text-center font-semibold uppercase tracking-[0.16em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-slate-600">
                    @forelse($suspendedUsers as $user)
                        <tr>
                            <td class="px-6 py-5 font-semibold text-slate-950">{{ $user->name }}</td>
                            <td class="px-6 py-5">{{ $user->role }}</td>
                            <td class="px-6 py-5">{{ $user->email }}</td>
                            <td class="px-6 py-5">{{ $user->updated_at->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-center">
                                {{-- Form points to your toggleStatus method --}}
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" 
                                    onsubmit="return confirm('Are you sure you want to restore access for {{ $user->name }}?');">
                                    @csrf
                                    <button type="submit" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition-all">
                                        Restore Access
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No suspended users</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
