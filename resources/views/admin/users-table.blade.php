@php
    $badgeClasses = [
        'Admin' => 'bg-violet-100 text-violet-700',
        'Staff' => 'bg-sky-100 text-sky-700',
        'Client' => 'bg-emerald-100 text-emerald-700',
    ];
    $statusClasses = [
        'Active' => 'bg-emerald-100 text-emerald-700',
        'Inactive' => 'bg-amber-100 text-amber-700',
        'Suspended' => 'bg-rose-100 text-rose-700',
    ];
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug delete forms
    document.querySelectorAll('form.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            console.log('Delete form submitted:', form.action);
            console.log('Method:', form.method);
            console.log('CSRF token:', form.querySelector('input[name="_token"]')?.value || form.querySelector('input[name="_token"]')?.value);
        });
    });
    
    // Also listen for any button clicks in the table
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            console.log('Delete button clicked');
        });
    });
});
</script>

<div class="mt-6 overflow-hidden rounded-3xl border border-slate-200" id="users-table-wrapper">
    <span id="users-table-count" class="hidden">{{ $users->count() }}</span>
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">User</th>
                <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Role</th>
                <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Status</th>
                <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Joined</th>
                <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.16em]">Last login</th>
                <th class="px-6 py-4 text-center font-semibold uppercase tracking-[0.16em]">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="flex h-11 w-11 items-center justify-center rounded-3xl bg-slate-100 text-sm font-semibold text-slate-800">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                {{-- Online Status Dot (Green if active in last 5 minutes) --}}
                                @if($user->last_login_at && $user->last_login_at->gt(now()->subMinutes(5)))
                                    <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white" title="Online"></span>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-slate-950">{{ $user->name }}</p>
                                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $badgeClasses[$user->role] ?? 'bg-slate-100 text-slate-700' }}">{{ $user->role }}</span>
                    </td>
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$user->account_status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $user->account_status }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-slate-600">{{ $user->created_at->format('M d, Y') }}</td>
                    
                    {{-- Updated Last Login Column --}}
                    <td class="px-6 py-5">
                        @if($user->last_login_at)
                            <p class="text-slate-950 font-medium">{{ $user->last_login_at->diffForHumans() }}</p>
                            <p class="text-xs text-slate-500">{{ $user->last_login_at->format('M d, h:i A') }}</p>
                        @else
                            <span class="text-slate-400 italic">Never</span>
                        @endif
                    </td>

                    <td class="px-6 py-5 text-center">
                        <div class="inline-flex items-center gap-2 text-slate-500">
                            <a href="{{ route('admin.users.show', $user) }}" class="rounded-full p-2 hover:bg-slate-100" title="View Details">
                                <span>👁️</span>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="rounded-full p-2 hover:bg-slate-100" title="Edit User">
                                <span>✏️</span>
                            </a>
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <button type="submit" class="rounded-full p-2 hover:bg-slate-100" title="{{ $user->account_status === 'Active' ? 'Suspend User' : 'Activate User' }}">
                                    <span>{{ $user->account_status === 'Active' ? '🔒' : '🔓' }}</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline delete-form"
                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full p-2 hover:bg-slate-100 delete-btn" title="Delete User">
                                    <span>🗑️</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">No users found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>