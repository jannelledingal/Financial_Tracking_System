<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialTrans;
use App\Models\ClientProfile;
use Illuminate\Support\Facades\Auth;

class StaffTransactionLogController extends Controller
{
    // Show all staff transactions (recent to oldest)
    public function index()
    {
        $staffId = Auth::id();
        $transactions = FinancialTrans::where('staff_id', $staffId)
            ->orderByDesc('created_at')
            ->with(['account', 'category'])
            ->paginate(20);
        return view('staff.transaction-log', compact('transactions'));
    }
}
