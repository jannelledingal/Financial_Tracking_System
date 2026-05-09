@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-950">Edit User</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-500">Update user information and settings</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">View Details</a>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Back to Users</a>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        @if(session('success'))
            <div class="mb-6 rounded-3xl bg-emerald-50 p-4 text-sm text-emerald-800 ring-1 ring-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-3xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-200">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" required />

                    <label class="block text-sm font-medium text-slate-700">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" required />

                    <label class="block text-sm font-medium text-slate-700">Role</label>
                    <select name="role" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" required>
                        <option value="Admin" {{ old('role', $user->role) === 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Staff" {{ old('role', $user->role) === 'Staff' ? 'selected' : '' }}>Staff</option>
                        <option value="Client" {{ old('role', $user->role) === 'Client' ? 'selected' : '' }}>Client</option>
                    </select>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700">Account Status</label>
                    <select name="account_status" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" required>
                        <option value="Active" {{ old('account_status', $user->account_status) === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('account_status', $user->account_status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Suspended" {{ old('account_status', $user->account_status) === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>

                    <div class="pt-4">
                        <p class="text-sm text-slate-500">
                            <strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}<br>
                            <strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection