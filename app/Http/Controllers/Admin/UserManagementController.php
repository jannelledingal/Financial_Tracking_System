<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\StaffAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules;


class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->with(['clientProfile', 'staffProfile'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        // Return table only for AJAX requests
        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.users-table', ['users' => $users])->render();
        }

        $totalUsers = User::count();
        $administrators = User::where('role', 'Admin')->count();
        $staffMembers = User::where('role', 'Staff')->count();
        $clients = User::where('role', 'Client')->count();
        $thisMonthUsers = User::where('created_at', '>=', now()->startOfMonth())->count();

        return view('admin.users', [
            'totalUsers' => $totalUsers,
            'administrators' => $administrators,
            'staffMembers' => $staffMembers,
            'clients' => $clients,
            'thisMonthUsers' => $thisMonthUsers,
            'users' => $users,
        ]);
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            // 1. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Staff', 
            ]);

            // 2. Create Staff Profile
            // This links the user to the staff side of your system
            StaffProfile::create([
                'user_id' => $user->id,
                
            ]);
        });

        return redirect()->route('admin.users')->with('status', 'Staff member created successfully!');
    }

    public function show(User $user): View
    {
        $user->load([
            'clientProfile',
            'staffProfile',
            'assignedClients',
        ]);

        return view('admin.user-detail', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.user-edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:Admin,Staff,Client'],
            'account_status' => ['required', 'in:Active,Inactive,Suspended'],
        ]);

        $user->update($request->only(['name', 'email', 'role', 'account_status']));

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $newStatus = $user->account_status === 'Active' ? 'Suspended' : 'Active';
        $user->update(['account_status' => $newStatus]);

        $message = $newStatus === 'Active' ? 'User account activated.' : 'User account suspended.';
        
        // Redirecting back keeps the admin on the "Suspended Users" page
        return redirect()->back()->with('success', $message);
    }

    public function destroy(User $user): RedirectResponse
{
    // 1. Safety check
    if ($user->id === auth()->id()) {
        return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
    }

    \DB::beginTransaction();

    try {
        if ($user->role === 'Client') {
            // 2. Delete the Client Profile
            // This triggers the CASCADE delete you set in phpMyAdmin for staff_assignments
            \DB::table('client_profiles')->where('user_id', $user->id)->delete();

            // 3. Clear accounts and transactions
            $accounts = \DB::table('accounts')->where('client_id', $user->id)->get();
            foreach ($accounts as $account) {
                \DB::table('financial_trans')->where('account_id', $account->id)->delete();
            }
            \DB::table('accounts')->where('client_id', $user->id)->delete();

        } elseif ($user->role === 'Staff') {
            // 4. Delete the Staff Profile
            // This also triggers CASCADE if staff_id links to staff_profiles
            \DB::table('staff_profiles')->where('user_id', $user->id)->delete();
            
            // Just in case staff_id links directly to user_id in staff_assignments:
            \DB::table('staff_assignments')->where('staff_id', $user->id)->delete();
        }

        // 5. Finally delete the main user record
        $user->delete();

        \DB::commit();
        return redirect()->route('admin.users')->with('success', 'User and all related records removed.');
        
    } catch (\Exception $e) {
        \DB::rollback();
        // This will now catch any remaining foreign key issues and tell you why
        return redirect()->route('admin.users')->with('error', 'Could not delete user: ' . $e->getMessage());
    }
}

    public function assignStaff(Request $request): RedirectResponse
{
    $request->validate([
        'staff_id' => ['required', 'exists:users,id'],
        'client_id' => ['required', 'exists:users,id'],
    ]);

    // 1. Find the Users and load their profiles
    $staffUser = User::with('staffProfile')->find($request->staff_id);
    $clientUser = User::with('clientProfile')->find($request->client_id);

    // 2. Safety check for roles
    if ($staffUser->role !== 'Staff' || $clientUser->role !== 'Client') {
        return redirect()->back()->with('error', 'Invalid assignment: Check user roles.');
    }

    // 3. IMPORTANT: Resolve BOTH Profile IDs
    $staffProfile = $staffUser->staffProfile;
    $clientProfile = $clientUser->clientProfile;

    if (!$staffProfile || !$clientProfile) {
        return redirect()->back()->with('error', 'Error: One of the users is missing a profile record.');
    }

    // 4. Check if assignment already exists using Profile IDs
    $existing = StaffAssignment::where('staff_id', $staffProfile->id)
        ->where('client_id', $clientProfile->id)
        ->exists();

    if ($existing) {
        return redirect()->back()->with('error', 'This assignment already exists.');
    }

    // 5. Use the Profile IDs for the insert
    StaffAssignment::create([
        'staff_id' => $staffProfile->id, // Use Staff Profile ID
        'client_id' => $clientProfile->id, // Use Client Profile ID
        'assigned_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Staff assigned to client successfully.');
}

    public function unassignStaff(StaffAssignment $assignment): RedirectResponse
    {
        $assignment->delete();

        return redirect()->back()->with('success', 'Staff unassigned from client successfully.');
    }

    
}
