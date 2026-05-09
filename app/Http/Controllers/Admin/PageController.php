<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffAssignment;
use App\Models\FinancialTrans;
use Illuminate\View\View;

class PageController extends Controller
{
    public function staffProfiles(): View
    {
        $staffMembers = User::where('role', 'Staff')->get();
        return view('admin.staff-profiles', ['staffMembers' => $staffMembers]);
    }

    public function clientProfiles(): View
    {
        $clients = User::where('role', 'Client')->get();
        return view('admin.client-profiles', ['clients' => $clients]);
    }

    public function suspendedUsers(): View
    {
        $suspendedUsers = User::where('account_status', 'Suspended')->get();
        return view('admin.suspended-users', ['suspendedUsers' => $suspendedUsers]);
    }

    public function rolePermissions(): View
    {
        return view('admin.role-permissions');
    }

    public function staffAssignments()
    {
       
        $assignments = StaffAssignment::with(['staffProfile.user', 'clientProfile.user'])
            ->get()
            ->groupBy('staff_id');

        return view('admin.staff-assignments', compact('assignments'));
    }

    public function auditLog(): View
    {
        return view('admin.audit-log');
    }

    public function allTransactions(): View
    {
        $transactions = FinancialTrans::with('account', 'category')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.all-transactions', ['transactions' => $transactions]);
    }

    public function reports(): View
    {
        return view('admin.reports');
    }

    public function systemSettings(): View
    {
        return view('admin.system-settings');
    }
}
