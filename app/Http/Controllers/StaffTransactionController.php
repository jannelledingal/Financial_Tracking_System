<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialTrans;
use App\Models\ClientProfile;
use App\Models\StaffAssignment;
use Illuminate\Support\Facades\Auth;

class StaffTransactionController extends Controller
{
    // Show Add Transaction form
    public function create()
    {
        $staffId = Auth::id();
        $assignedClients = StaffAssignment::where('staff_id', $staffId)
            ->with('client')
            ->get()
            ->pluck('client');
        return view('staff.add-transaction', compact('assignedClients'));
    }

    // Store new transaction
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:client_profiles,id',
            'type' => 'required|in:expense,withdrawal',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        FinancialTrans::create([
            'client_id' => $request->client_id,
            'staff_id' => Auth::id(),
            'type' => $request->type,
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return redirect()->back()->with('success', 'Transaction added successfully.');
    }
}
