@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-950">User Management</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-500">Manage all system users — roles and assignments.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            
            <a href="#add-staff" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Add staff</a>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-4">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Total users</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $totalUsers }}</p>
            <p class="mt-3 text-sm text-slate-500"><span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $thisMonthUsers > 0 ? '+' . $thisMonthUsers : 'No change' }} this month</span></p>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Administrators</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $administrators }}</p>
            <p class="mt-3 text-sm text-slate-500">No change</p>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Staff members</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $staffMembers }}</p>
            <p class="mt-3 text-sm text-slate-500"><span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700\">Most active</span></p>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Clients</p>
            <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $clients }}</p>
            <p class="mt-3 text-sm text-slate-500"><span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700\">Growing</span></p>
        </div>
    </div>

    
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <form id="user-filters-form" action="{{ route('admin.users') }}" method="GET" class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <input type="hidden" name="role" id="user-filter-role" value="{{ request('role') }}" />

            <div class="relative max-w-xl flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">🔍</span>
                <input type="search" name="search" id="user-filter-search" value="{{ request('search') }}" class="w-full rounded-full border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" placeholder="Search by name, email, or role..." />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-2 inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                    Filtered Results
                    <span id="users-filter-count" class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-slate-200 px-2 text-xs text-slate-600">{{ $users->count() }}</span>
                </span>

                <button type="button" id="user-filter-all" data-role="" class="rounded-full border px-4 py-2 text-sm font-medium transition-colors {{ !request('role') ? 'bg-slate-950 text-white border-slate-950' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">All</button>

                @foreach(['Admin', 'Staff', 'Client'] as $role)
                    <button type="button" data-role="{{ $role }}" class="rounded-full border px-4 py-2 text-sm font-medium transition-colors {{ request('role') == $role ? 'bg-slate-950 text-white border-slate-950' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">{{ $role }}</button>
                @endforeach
            </div>
        </form>

        @include('admin.users-table', ['users' => $users])
    </div>


    <div id="add-staff" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Create staff member</h2>
                <p class="mt-2 text-sm text-slate-500">Use this section to add new staff accounts. Clients register themselves through the welcome page.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-4 rounded-3xl bg-emerald-50 p-4 text-sm text-emerald-800 ring-1 ring-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-4 rounded-3xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-200">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.staff.store') }}" method="POST" class="mt-6 grid gap-4 xl:grid-cols-2">
            @csrf

            <div class="space-y-4">
                <label class="block text-sm font-medium text-slate-700">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" />

                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" />
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" />

                <label class="block text-sm font-medium text-slate-700">Confirm password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200" />
            </div>

            <div class="xl:col-span-2 text-right">
                <input type="hidden" name="role" value="Staff" />
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Create staff account</button>
            </div>
        </form>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Role permissions matrix</h2>
                </div>
                <a href="{{ route('admin.role-permissions') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-500">permissions ↗</a>
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

        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('user-filters-form');
        const searchInput = document.getElementById('user-filter-search');
        const roleInput = document.getElementById('user-filter-role');
        const tableWrapperId = 'users-table-wrapper';
        const tableWrapper = document.getElementById(tableWrapperId);
        const roleButtons = Array.from(form.querySelectorAll('button[data-role]'));
        const allButton = document.getElementById('user-filter-all');
        let debounceTimer = null;

        console.log('Form loaded:', !!form);
        console.log('Search input:', !!searchInput);
        console.log('Role input:', !!roleInput);
        console.log('Table wrapper:', !!tableWrapper);
        console.log('Role buttons found:', roleButtons.length);
        console.log('All button:', !!allButton);

        const updateActiveState = (selectedRole) => {
            roleButtons.forEach((button) => {
                const isActive = button.dataset.role === selectedRole;
                button.classList.toggle('bg-slate-950', isActive);
                button.classList.toggle('text-white', isActive);
                button.classList.toggle('border-slate-950', isActive);
                button.classList.toggle('bg-white', !isActive);
                button.classList.toggle('text-slate-700', !isActive);
                button.classList.toggle('border-slate-200', !isActive);
            });
            if (allButton) {
                const isActive = selectedRole === '';
                allButton.classList.toggle('bg-slate-950', isActive);
                allButton.classList.toggle('text-white', isActive);
                allButton.classList.toggle('border-slate-950', isActive);
                allButton.classList.toggle('bg-white', !isActive);
                allButton.classList.toggle('text-slate-700', !isActive);
                allButton.classList.toggle('border-slate-200', !isActive);
            }
        };

        const fetchUsers = () => {
            console.log('Fetching users...');
            const params = new URLSearchParams(new FormData(form));
            const url = `${form.action}?${params.toString()}`;
            console.log('Request URL:', url);

            const currentWrapper = document.getElementById(tableWrapperId);
            if (!currentWrapper) {
                console.error('Table wrapper element not found');
                return;
            }

            currentWrapper.style.opacity = '0.5';
            currentWrapper.style.pointerEvents = 'none';

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => {
                    console.log('Server responded with status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.text();
                })
                .then((html) => {
                    console.log('Received HTML, length:', html.length);
                    const wrapper = document.getElementById(tableWrapperId);
                    if (wrapper) {
                        // Replace the entire wrapper with the new HTML
                        wrapper.outerHTML = html;
                        console.log('Table replaced successfully');
                        
                        // Update the count display
                        const newCount = document.querySelector('#users-table-count');
                        const filterCount = document.getElementById('users-filter-count');
                        if (newCount && filterCount) {
                            filterCount.textContent = newCount.textContent.trim();
                        }
                        
                        window.history.replaceState({}, '', url);
                    }
                })
                .catch((error) => {
                    console.error('AJAX Error:', error);
                    const wrapper = document.getElementById(tableWrapperId);
                    if (wrapper) {
                        wrapper.style.opacity = '1';
                        wrapper.style.pointerEvents = 'auto';
                    }
                });
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
        });

        searchInput.addEventListener('input', () => {
            console.log('Search input changed');
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchUsers, 300);
        });

        if (allButton) {
            allButton.addEventListener('click', () => {
                console.log('All button clicked');
                roleInput.value = '';
                updateActiveState('');
                fetchUsers();
            });
        }

        roleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                console.log('Role button clicked:', button.dataset.role);
                roleInput.value = button.dataset.role;
                updateActiveState(button.dataset.role);
                fetchUsers();
            });
        });
    });
</script>
@endsection
