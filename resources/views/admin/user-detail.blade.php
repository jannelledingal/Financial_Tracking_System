@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-950">User Details</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-500">Detailed information about {{ $user->name }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Edit User</a>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Back to Users</a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <h2 class="text-lg font-semibold text-slate-950 mb-6">Profile Information</h2>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Full Name</label>
                            <p class="mt-1 text-sm text-slate-900">{{ $user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email Address</label>
                            <p class="mt-1 text-sm text-slate-900">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Role</label>
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                @if($user->role === 'Admin') bg-violet-100 text-violet-700
                                @elseif($user->role === 'Staff') bg-sky-100 text-sky-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ $user->role }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Account Status</label>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
                                @if($user->account_status === 'Active') bg-emerald-100 text-emerald-700
                                @elseif($user->account_status === 'Suspended') bg-rose-100 text-rose-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $user->account_status }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Joined Date</label>
                            <p class="mt-1 text-sm text-slate-900">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Last Login</label>
                            <p class="mt-1 text-sm text-slate-900">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            

        <div class="space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <h2 class="text-lg font-semibold text-slate-950 mb-4">Quick Actions</h2>

                <div class="space-y-3">
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="block">
                        @csrf
                        @method('POST')
                        <button type="submit" class="w-full rounded-full px-4 py-3 text-sm font-semibold
                            @if($user->account_status === 'Active') bg-rose-100 text-rose-700 hover:bg-rose-200
                            @else bg-emerald-100 text-emerald-700 hover:bg-emerald-200 @endif">
                            {{ $user->account_status === 'Active' ? 'Suspend Account' : 'Activate Account' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="block"
                          onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-full bg-rose-100 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-200">
                            Delete User
                        </button>
                    </form>
                </div>
            </div>

            @if($user->role === 'Staff')
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <h2 class="text-lg font-semibold text-slate-950 mb-4">Assigned Clients</h2>

                <div class="space-y-3">
                    @forelse($user->assignedClients as $assignment)
                        @php $client = $assignment->clientProfile->user ?? null; @endphp
                        <div class="flex items-center gap-3 rounded-3xl bg-slate-50 p-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-3xl bg-slate-200 text-xs font-semibold text-slate-700">
                                {{ strtoupper(substr($client->name ?? 'NA', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-950">{{ $client->name ?? 'Unknown Client' }}</p>
                                <p class="text-xs text-slate-500">{{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y') : 'Assigned date unavailable' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No clients assigned</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection