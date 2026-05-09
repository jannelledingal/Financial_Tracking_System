<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\FinancialTrans;
use App\Models\Message;
use App\Models\StaffAssignment;
use App\Models\ClientProfile;
use App\Models\TransactionImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * Helper method to get assigned Client User IDs for the authenticated staff.
     */
    private function getAssignedClientUserIds()
    {
        $staff = Auth::user()->load('staffProfile');
        if (!$staff->staffProfile) return collect();

        return StaffAssignment::where('staff_id', $staff->staffProfile->id)
            ->with('clientProfile')
            ->get()
            ->pluck('clientProfile.user_id');
    }

    /**
     * Helper method to get assigned Client Profile IDs for the authenticated staff.
     * This is needed because accounts.client_id references client_profiles.id
     */
    private function getAssignedClientProfileIds()
    {
        $staff = Auth::user()->load('staffProfile');
        if (!$staff->staffProfile) return collect();

        return StaffAssignment::where('staff_id', $staff->staffProfile->id)
            ->pluck('client_id');
    }

    public function index(): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();

        // Get assigned clients - use profileAccounts() for correct profile ID
        $assignedClients = User::whereIn('id', $clientIds)->with('clientProfile')->get();

        // Get Profile IDs for account queries (client_id in accounts references client_profiles.id)
        $profileIds = $assignedClients->pluck('clientProfile.id')->filter();
        
        // Get account IDs for these clients using profile IDs
        $accountIds = Account::whereIn('client_id', $profileIds)->pluck('id');
 
        $totalTransactionsLogged = FinancialTrans::whereIn('account_id', $accountIds)->count();
        $transactions = FinancialTrans::with(['category', 'account'])
            ->whereIn('account_id', $accountIds)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        $incomeTotal = $transactions->where('type', 'Income')->sum('amount');
        $expenseTotal = abs($transactions->where('type', 'Expense')->sum('amount'));

        $monthlyTransactions = FinancialTrans::selectRaw('MONTH(created_at) as month, type, COUNT(*) as total')
            ->whereIn('account_id', $accountIds)
            ->whereYear('created_at', now()->year)
            ->groupBy('month', 'type')
            ->get();

        $monthlyIncome = array_fill(1, 12, 0);
        $monthlyExpense = array_fill(1, 12, 0);

        foreach ($monthlyTransactions as $row) {
            if ($row->type === 'Income') $monthlyIncome[$row->month] = (int) $row->total;
            if ($row->type === 'Expense') $monthlyExpense[$row->month] = (int) $row->total;
        }

        $messages = Message::with('sender')
            ->where('receiver_id', $staff->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $pendingReviews = Message::where('receiver_id', $staff->id)->where('is_read', false)->count();
        $reportsGenerated = max(1, (int) floor($transactions->count() / 8));

        $staffPerformance = [
            ['label' => 'Client satisfaction', 'value' => 94, 'color' => 'emerald'],
            ['label' => 'Reporting accuracy', 'value' => 97, 'color' => 'sky'],
            ['label' => 'Task completion', 'value' => 83, 'color' => 'violet'],
            ['label' => 'Response time', 'value' => 78, 'color' => 'amber'],
            ['label' => 'On-time reports', 'value' => 90, 'color' => 'teal'],
        ];

        return view('staff.dashboard', [
            'staff' => $staff,
            'assignedClients' => $assignedClients,
            'totalClients' => $assignedClients->count(),
            'transactionsLogged' => $totalTransactionsLogged,
            'pendingReviews' => $pendingReviews,
            'reportsGenerated' => $reportsGenerated,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'transactions' => $transactions,
            'monthlyIncome' => array_values($monthlyIncome),
            'monthlyExpense' => array_values($monthlyExpense),
            'messages' => $messages,
            'staffPerformance' => $staffPerformance,
        ]);
    }

    public function clients(): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();
        $clients = User::whereIn('id', $clientIds)->with(['clientProfile', 'accounts'])->get();

        return view('staff.clients', compact('staff', 'clients'));
    }

    public function showClient(Request $request, User $client)
{
    // 1. Security check
    if (!$this->getAssignedClientUserIds()->contains($client->id)) {
        abort(403, 'Unauthorized access to this client.');
    }

    // 2. Date Logic
    $period = $request->get('period', 'year');
    $customDate = $request->get('custom_date');
    $now = Carbon::now();

    if ($customDate) {
        $startDate = Carbon::parse($customDate)->startOfDay();
        $endDate = Carbon::parse($customDate)->endOfDay();
    } else {
        $startDate = match($period) {
            'today' => $now->copy()->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            default => $now->copy()->startOfYear(),
        };
        $endDate = match($period) {
            'today' => $now->copy()->endOfDay(),
            'week' => $now->copy()->endOfWeek(),
            'month' => $now->copy()->endOfMonth(),
            default => $now->copy()->endOfYear(),
        };
    }

    // 3. Fetch accounts using the Profile ID (client_id in accounts table references client_profiles.id)
    $clientProfileId = $client->clientProfile->id;
    $accounts = Account::where('client_id', $clientProfileId)->get();
    $accountIds = $accounts->pluck('id');

    // Fetch transactions for those specific accounts
    $allTransactions = FinancialTrans::whereIn('account_id', $accountIds)
        ->with('category')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

    // 4. Calculate Totals
    $incomeTotal = $allTransactions->where('type', 'Income')->sum('amount');
    $expenseTotal = abs($allTransactions->where('type', 'Expense')->sum('amount'));
    $balanceTotal = $accounts->sum('balance');

    // 5. Chart Data Initialization
    $monthlyIncome = []; $monthlyExpense = []; $labels = [];

    if ($period == 'year') {
        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        foreach (range(1, 12) as $m) {
            $monthlyIncome[] = $allTransactions->where('type', 'Income')->filter(fn($t) => $t->created_at->month == $m)->sum('amount');
            $monthlyExpense[] = abs($allTransactions->where('type', 'Expense')->filter(fn($t) => $t->created_at->month == $m)->sum('amount'));
        }
    } else {
        $days = $startDate->diffInDays($endDate) + 1;
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $labels[] = $date->format('M d');
            $monthlyIncome[] = $allTransactions->where('type', 'Income')->filter(fn($t) => $t->created_at->isSameDay($date))->sum('amount');
            $monthlyExpense[] = abs($allTransactions->where('type', 'Expense')->filter(fn($t) => $t->created_at->isSameDay($date))->sum('amount'));
        }
    }

    // 6. Ensure spendingByCategory is an Array
    $spendingByCategory = $allTransactions->where('type', 'Expense')
        ->groupBy('category_id')
        ->map(function($group) {
            return [
                'name' => $group->first()->category->category_name ?? 'Misc',
                'amount' => abs($group->sum('amount'))
            ];
        })->values()->toArray();

    return view('staff.show-client', compact(
        'client', 
        'incomeTotal', 
        'expenseTotal', 
        'balanceTotal', 
        'monthlyIncome', 
        'monthlyExpense', 
        'labels', 
        'spendingByCategory',
        'accounts'
    ))->with('transactions', $allTransactions);
}

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:Income,Expense',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
        ]);

        $account = Account::findOrFail($request->account_id);
        // FIX: Use profile IDs instead of user IDs for authorization
        if (!$this->getAssignedClientProfileIds()->contains($account->client_id)) {
            return redirect()->back()->with('error', 'Unauthorized access to this account.');
        }

        if ($request->type === 'Expense' && $account->balance < $request->amount) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        $amount = $request->type === 'Expense' ? -$request->amount : $request->amount;
        FinancialTrans::create([
            'account_id' => $account->id,
            'type' => $request->type,
            'amount' => $amount,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        $request->type === 'Income' ? $account->increment('balance', $request->amount) : $account->decrement('balance', $request->amount);

        return redirect()->back()->with('success', 'Transaction added successfully!');
    }

    public function transactionLog(): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();
        
        // Get assigned clients with their profiles to get Profile IDs
        $assignedClients = User::whereIn('id', $clientIds)->with('clientProfile')->get();
        $profileIds = $assignedClients->pluck('clientProfile.id')->filter();
        
        $accountIds = Account::whereIn('client_id', $profileIds)->pluck('id');

        $transactions = FinancialTrans::with(['category', 'account.user']) 
            ->whereIn('account_id', $accountIds)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('staff.transaction-log', compact('staff', 'transactions'));
    }

    /**
     * Show edit form for a transaction
     */
    public function editTransaction(FinancialTrans $transaction)
    {
        $this->authorizeTransactionAccess($transaction);
        
        return view('staff.modals.edit-transaction-modal', [
            'transaction' => $transaction->load(['account', 'category']),
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update a transaction with password validation
     */
    public function updateTransaction(Request $request, FinancialTrans $transaction)
    {
        $this->authorizeTransactionAccess($transaction);
        
        // Verify password
        if ($request->password !== 'FinTrackPass2026!') {
            return back()->with('error', 'Invalid password. Transaction update cancelled.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'edit_reason' => 'required|string|max:500',
        ]);

        // Store original amount before update
        if (is_null($transaction->original_amount)) {
            $transaction->original_amount = $transaction->amount;
        }

        $transaction->update([
            'amount' => $transaction->type === 'Expense' ? -abs($validated['amount']) : $validated['amount'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'edited_by' => Auth::id(),
            'edit_reason' => $validated['edit_reason'],
        ]);

        $this->recalculateAccountBalance($transaction->account_id);

        return redirect()->route('staff.transaction-log')->with('success', 'Transaction updated and account synced successfully.');
    }

    /**
     * Show void confirmation form
     */
    public function showVoidTransaction(FinancialTrans $transaction)
    {
        $this->authorizeTransactionAccess($transaction);

        return view('staff.modals.void-transaction-modal', [
            'transaction' => $transaction->load(['account', 'category']),
        ]);
    }

    /**
     * Void/delete a transaction with password validation
     */
    public function voidTransaction(Request $request, FinancialTrans $transaction)
    {
        $this->authorizeTransactionAccess($transaction);
        
        // Verify password
        if ($request->password !== 'FinTrackPass2026!') {
            return back()->with('error', 'Invalid password. Transaction void cancelled.');
        }

        $request->validate([
            'void_reason' => 'required|string|max:500',
        ]);

        $transaction->update([
            'voided_by' => Auth::id(),
            'void_reason' => $request->void_reason,
        ]);

        $transaction->delete(); // Soft delete
        $this->recalculateAccountBalance($transaction->account_id);

        return redirect()->route('staff.transaction-log')->with('success', 'Transaction voided and account synced successfully.');
    }

    /**
     * Sync transactions with account balances
     */
    public function syncTransactions(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
        ]);

        $this->recalculateAccountBalance($request->account_id);

        return back()->with('success', 'Account balance synced successfully.');
    }

    public function syncAllTransactions()
    {
        $profileIds = $this->getAssignedClientProfileIds();
        $accountIds = Account::whereIn('client_id', $profileIds)->pluck('id');

        foreach ($accountIds as $accountId) {
            $this->recalculateAccountBalance($accountId);
        }

        return back()->with('success', 'All assigned account balances synced successfully.');
    }

    private function recalculateAccountBalance(int $accountId): void
    {
        $transactions = FinancialTrans::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->get();

        $balance = $transactions->sum('amount');

        Account::where('id', $accountId)->update(['balance' => max(0, $balance)]);
    }

    /**
     * Authorize transaction access - staff can only edit/void their own transactions
     */
    private function authorizeTransactionAccess(FinancialTrans $transaction)
    {
        $staff = Auth::user();
        $profileIds = $this->getAssignedClientProfileIds();
        
        $accountIds = Account::whereIn('client_id', $profileIds)->pluck('id');

        if (!$accountIds->contains($transaction->account_id)) {
            abort(403, 'Unauthorized to modify this transaction.');
        }
    }

    public function generateReport(Request $request): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();
        $assignedClients = User::whereIn('id', $clientIds)->with(['clientProfile', 'accounts'])->get();
        
        $reportData = null;
        if ($request->filled('client_id')) {
            $client = $assignedClients->firstWhere('id', $request->client_id);
            if ($client) {
                $accountIds = $client->profileAccounts()->pluck('id');
                $transactions = FinancialTrans::with(['category', 'account'])
                    ->whereIn('account_id', $accountIds)->orderByDesc('created_at')->get();
                
                $totalIncome = $transactions->where('type', 'Income')->sum('amount');
                $totalExpense = abs($transactions->where('type', 'Expense')->sum('amount'));

                $reportData = [
                    'client' => $client,
                    'transactions' => $transactions->take(50),
                    'totalIncome' => $totalIncome,
                    'totalExpense' => $totalExpense,
                    'netBalance' => $totalIncome - $totalExpense,
                    'monthlyData' => $transactions->groupBy(fn($t) => $t->created_at->format('Y-m'))->map(fn($g) => [
                        'income' => $g->where('type', 'Income')->sum('amount'),
                        'expense' => abs($g->where('type', 'Expense')->sum('amount')),
                    ]),
                ];
            }
        }

        return view('staff.generate-report', [
            'staff' => $staff,
            'assignedClients' => $assignedClients,
            'clientCount' => $assignedClients->count(),
            'reportCount' => max(1, (int) floor($assignedClients->count() / 2)),
            'reportData' => $reportData,
        ]);
    }

    public function reports(Request $request)
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();
        $assignedClients = User::whereIn('id', $clientIds)->with(['clientProfile', 'accounts'])->get();

        $reportData = null;
        if ($request->filled('client_id')) {
            $client = $assignedClients->firstWhere('id', $request->client_id);
            if ($client) {
                $period = $request->get('period', 'overall');
                $now = Carbon::now();
                
                $startDate = match($period) {
                    'day' => $now->copy()->startOfDay(),
                    'week' => $now->copy()->startOfWeek(),
                    'month' => $now->copy()->startOfMonth(),
                    'year' => $now->copy()->startOfYear(),
                    default => $now->copy()->subYear(10),
                };
                
                $accountIds = $client->profileAccounts()->pluck('id');
                $transactions = FinancialTrans::with(['category', 'account'])
                    ->whereIn('account_id', $accountIds)
                    ->where('created_at', '>=', $startDate)
                    ->orderByDesc('created_at')
                    ->get();
                
                $totalIncome = $transactions->where('type', 'Income')->sum('amount');
                $totalExpense = abs($transactions->where('type', 'Expense')->sum('amount'));

                $periodLabel = match($period) {
                    'day' => 'Daily Report',
                    'week' => 'Weekly Report',
                    'month' => 'Monthly Report',
                    'year' => 'Annual Report',
                    default => 'Overall Report',
                };

                $reportData = [
                    'client' => $client,
                    'transactions' => $transactions->take(50),
                    'totalIncome' => $totalIncome,
                    'totalExpense' => $totalExpense,
                    'netBalance' => $totalIncome - $totalExpense,
                    'periodLabel' => $periodLabel,
                    'period' => $period,
                ];

                // Handle CSV download
                if ($request->has('download')) {
                    return $this->downloadReportAsCSV($reportData);
                }
            }
        }

        return view('staff.reports', [
            'staff' => $staff,
            'assignedClients' => $assignedClients,
            'reportData' => $reportData,
        ]);
    }

    /**
     * Generate and download the report as CSV
     */
    private function downloadReportAsCSV($reportData)
    {
        $filename = 'report_' . $reportData['client']->name . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Write header info
            fputcsv($file, ['Financial Statement Report']);
            fputcsv($file, [$reportData['periodLabel']]);
            fputcsv($file, ['Client', $reportData['client']->name]);
            fputcsv($file, ['Generated on', now()->format('M d, Y H:i A')]);
            fputcsv($file, ['Generated by', Auth::user()->name]);
            fputcsv($file, []);
            
            // Write summary
            fputcsv($file, ['Summary']);
            fputcsv($file, ['Total Income', '₱' . number_format($reportData['totalIncome'], 2)]);
            fputcsv($file, ['Total Expenses', '₱' . number_format($reportData['totalExpense'], 2)]);
            fputcsv($file, ['Net Balance', '₱' . number_format($reportData['netBalance'], 2)]);
            fputcsv($file, []);
            
            // Write transaction headers
            fputcsv($file, ['Date', 'Account', 'Category', 'Type', 'Amount']);
            
            // Write transactions
            foreach ($reportData['transactions'] as $trans) {
                fputcsv($file, [
                    $trans->created_at->format('M d, Y'),
                    $trans->account->account_type,
                    $trans->category->category_name,
                    $trans->type,
                    number_format(abs($trans->amount), 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPath = $request->hasFile('attachment') ? $request->file('attachment')->store('message-attachments', 'public') : null;
        $attachmentName = $request->hasFile('attachment') ? $request->file('attachment')->getClientOriginalName() : null;

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content ?? '',
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    public function profile(): View
    {
        return view('staff.profile', ['staff' => Auth::user()]);
    }

    public function addTransaction(): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();

        // Get assigned clients with their profiles to get Profile IDs
        $assignedClients = User::whereIn('id', $clientIds)->with('clientProfile')->get();
        $profileIds = $assignedClients->pluck('clientProfile.id')->filter();

        // 1. Get the accounts for the "Log Transaction" section
        $accounts = Account::whereIn('client_id', $profileIds)
            ->with('user')
            ->get();

        // 2. Get the client users for the "Add Client Account" section
        $clients = User::whereIn('id', $clientIds)->get();

        // 3. Get the categories for the transaction dropdown
        $categories = Category::all();

        // Pass all three required variables to the view
        return view('staff.add-transaction', compact('staff', 'accounts', 'clients', 'categories'));
    }

    public function incomeRecords(Request $request): View
    {
        $period = $request->get('period', 'month');
        $clientIds = $this->getAssignedClientUserIds();
        
        // Get assigned clients with their profiles to get Profile IDs
        $assignedClients = User::whereIn('id', $clientIds)->with('clientProfile')->get();
        $profileIds = $assignedClients->pluck('clientProfile.id')->filter();
        
        $accountIds = Account::whereIn('client_id', $profileIds)->pluck('id');

        $startDate = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfYear(),
        };

        $transactions = FinancialTrans::with(['category', 'account.user'])
            ->whereIn('account_id', $accountIds)
            ->where('type', 'Income')
            ->where('created_at', '>=', $startDate)
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalIncome = FinancialTrans::whereIn('account_id', $accountIds)
            ->where('type', 'Income')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        // --- ADD THIS LOGIC FOR THE CHART ---
        $chartLabels = [];
        $chartValues = [];

        // Simple daily breakdown for the chart
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('M d');
            $chartValues[] = FinancialTrans::whereIn('account_id', $accountIds)
                ->where('type', 'Income')
                ->whereDate('created_at', $date)
                ->sum('amount');
        }

        return view('staff.income-records', compact(
            'transactions', 
            'totalIncome', 
            'period', 
            'chartLabels', // Required by blade
            'chartValues'  // Required by blade
        ));
    }

    public function expenseRecords(Request $request): View
    {
        $period = $request->get('period', 'month');
        $clientIds = $this->getAssignedClientUserIds();
        
        // Get assigned clients with their profiles to get Profile IDs
        $assignedClients = User::whereIn('id', $clientIds)->with('clientProfile')->get();
        $profileIds = $assignedClients->pluck('clientProfile.id')->filter();
        
        $accountIds = Account::whereIn('client_id', $profileIds)->pluck('id');

        $startDate = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfYear(),
        };

        $transactions = FinancialTrans::with(['category', 'account.user'])
            ->whereIn('account_id', $accountIds)
            ->where('type', 'Expense')
            ->where('created_at', '>=', $startDate)
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalExpense = FinancialTrans::whereIn('account_id', $accountIds)
            ->where('type', 'Expense')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        // --- ADD THIS LOGIC FOR THE CHART ---
        $chartLabels = [];
        $chartValues = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('M d');
            $chartValues[] = abs(FinancialTrans::whereIn('account_id', $accountIds)
                ->where('type', 'Expense')
                ->whereDate('created_at', $date)
                ->sum('amount'));
        }

        return view('staff.expense-records', compact(
            'transactions', 
            'totalExpense', 
            'period', 
            'chartLabels', // Required by blade
            'chartValues'  // Required by blade
        ));
    }

    public function clientMessages(): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();

        $messages = Message::where(function($q) use ($staff) {
                $q->where('receiver_id', $staff->id)
                ->orWhere('sender_id', $staff->id);
            })
            ->where(function($q) use ($clientIds) {
                $q->whereIn('sender_id', $clientIds)
                ->orWhereIn('receiver_id', $clientIds);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function($message) use ($staff) {
                // Grouping by the client's ID
                return $message->sender_id === $staff->id ? $message->receiver_id : $message->sender_id;
            });

        return view('staff.client-messages', compact('messages'));
    }

    public function getConversation($clientId): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();

        if (!$clientIds->contains($clientId)) {
            abort(403, 'You are not assigned to this client.');
        }

        // Rename $client to $otherUser to match your blade file
        $otherUser = User::findOrFail($clientId);

        $messages = Message::where(function($q) use ($staff, $clientId) {
                $q->where('sender_id', $staff->id)->where('receiver_id', $clientId);
            })
            ->orWhere(function($q) use ($staff, $clientId) {
                $q->where('sender_id', $clientId)->where('receiver_id', $staff->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $clientId)
            ->where('receiver_id', $staff->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Pass $otherUser instead of $client
        return view('staff.conversation', compact('otherUser', 'messages'));
    }

    public function createAccount(): View
    {
        $staff = Auth::user();
        $clientIds = $this->getAssignedClientUserIds();
        $clients = User::whereIn('id', $clientIds)->get();

        return view('staff.create-account', compact('staff', 'clients'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'client_id'    => 'required|exists:users,id', // User ID from dropdown
            'account_type' => 'required|string',
            'balance'      => 'required|numeric|min:0',
            'currency'     => 'required|string|max:10',
        ]);

        // 1. Security Check (ensure staff is assigned to this user)
        $assignedClientIds = $this->getAssignedClientUserIds();
        if (!$assignedClientIds->contains($request->client_id)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // 2. Verify the user has a client profile
        $clientUser = User::with('clientProfile')->findOrFail($request->client_id);
        
        if (!$clientUser->clientProfile) {
            return redirect()->back()->with('error', 'This user does not have a client profile.');
        }

        // 3. Save using the PROFILE ID to satisfy the database foreign key
        \App\Models\Account::create([
            'client_id'    => $clientUser->clientProfile->id, // Use Profile ID for FK constraint
            'account_type' => $request->account_type,
            'balance'      => $request->balance,
            'currency'     => $request->currency,
        ]);

        return redirect()->back()->with('success', 'New account created successfully!');
    }

    public function editAccount(Account $account): View
    {
        // Security check: ensure staff is assigned to this account's client
        $profileIds = $this->getAssignedClientProfileIds();
        if (!$profileIds->contains($account->client_id)) {
            abort(403, 'Unauthorized access to this account.');
        }

        $client = User::whereHas('clientProfile', function($q) use ($account) {
            $q->where('id', $account->client_id);
        })->first();

        return view('staff.edit-account', compact('account', 'client'));
    }

    public function updateAccount(Request $request, Account $account)
    {
        // Security check: ensure staff is assigned to this account's client
        $profileIds = $this->getAssignedClientProfileIds();
        if (!$profileIds->contains($account->client_id)) {
            abort(403, 'Unauthorized access to this account.');
        }

        $request->validate([
            'account_type' => 'required|string',
            'currency' => 'required|string|max:10',
        ]);

        $account->update([
            'account_type' => $request->account_type,
            'currency' => $request->currency,
        ]);

        return redirect()->route('staff.show-client', $account->client_id)
            ->with('success', 'Account updated successfully!');
    }

    public function destroyAccount(Account $account)
    {
        // Security check: ensure staff is assigned to this account's client
        $profileIds = $this->getAssignedClientProfileIds();
        if (!$profileIds->contains($account->client_id)) {
            abort(403, 'Unauthorized access to this account.');
        }

        // Check if account has transactions
        if ($account->transactions()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete account with existing transactions.');
        }

        $account->delete();

        return redirect()->back()->with('success', 'Account deleted successfully!');
    }

    /**
     * Export transactions to CSV
     */
    public function exportTransactions($clientId)
    {
        $profileIds = $this->getAssignedClientProfileIds();
        
        $client = ClientProfile::findOrFail($clientId);
        if (!$profileIds->contains($clientId)) {
            abort(403, 'Unauthorized to export transactions for this client.');
        }

        $accounts = Account::where('client_id', $clientId)->pluck('id');
        $transactions = FinancialTrans::whereIn('account_id', $accounts)
            ->with(['category', 'account'])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'transactions_' . $client->user->name . '_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['date', 'account_type', 'category', 'description', 'type', 'amount'];
        $callback = function() use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($transactions as $trans) {
                fputcsv($file, [
                    $trans->created_at->format('Y-m-d H:i:s'),
                    $trans->account->account_type,
                    $trans->category?->category_name ?? 'N/A',
                    $trans->description,
                    $trans->type,
                    $trans->amount,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import transactions from CSV
     */
    public function importTransactions(Request $request, User $client)
    {
        if (!$this->getAssignedClientUserIds()->contains($client->id)) {
            abort(403, 'Unauthorized to import transactions for this client.');
        }

        $clientProfile = $client->clientProfile;
        if (!$clientProfile) {
            abort(404, 'Client profile not found.');
        }

        $profileId = $clientProfile->id;

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            $handle = fopen($path, 'r');
            if (!$handle) {
                return redirect()->back()->with('error', 'Unable to read the uploaded CSV file.');
            }

            $headerRow = fgetcsv($handle);
            if (!$headerRow) {
                fclose($handle);
                return redirect()->back()->with('error', 'CSV file is empty or malformed.');
            }

            $headerRow[0] = preg_replace('/^\xef\xbb\xbf/', '', $headerRow[0]);
            $header = array_map(fn ($value) => strtolower(trim($value)), $headerRow);
            $fieldMap = array_flip($header);

            $getIndex = function (array $keys) use ($fieldMap) {
                foreach ($keys as $key) {
                    if (isset($fieldMap[$key])) {
                        return $fieldMap[$key];
                    }
                }
                return null;
            };

            $accountIndex = $getIndex(['account_id', 'account number', 'account number', 'account', 'account_type', 'account type']);
            $categoryIndex = $getIndex(['category_id', 'category name', 'category', 'category_name']);
            $descriptionIndex = $getIndex(['description', 'details', 'note', 'notes']);
            $typeIndex = $getIndex(['type', 'transaction_type', 'transaction type', 'category_type']);
            $amountIndex = $getIndex(['amount', 'transaction_amount', 'transaction amount', 'value', 'total']);
            $dateIndex = $getIndex(['created_at', 'date', 'transaction_date', 'transaction date', 'datetime', 'timestamp']);
            $updatedDateIndex = $getIndex(['updated_at', 'updated date', 'updated date time', 'updated datetime']);

            $importedTransactions = [];
            $rowCount = 0;
            $skippedRows = [];
            $errors = [];

            if ($accountIndex === null) {
                $errors[] = 'No account column found. Expected: account_id, account_type, or account.';
            }

            while (($row = fgetcsv($handle)) !== false) {
                if (empty(array_filter($row, fn ($value) => trim((string)$value) !== ''))) {
                    continue;
                }

                $rowCount++;
                $row = array_map(fn ($value) => trim((string)$value), $row);

                $account = null;
                $categoryId = null;
                $description = '';
                $type = 'Income';
                $amount = 0;
                $date = null;
                $updatedAt = null;

                $accountValue = null;
                if ($accountIndex !== null && isset($row[$accountIndex])) {
                    $accountValue = trim($row[$accountIndex]);
                }

                if ($accountValue !== null && $accountValue !== '') {
                    if (is_numeric($accountValue)) {
                        $account = Account::where('client_id', $profileId)
                            ->where('id', (int)$accountValue)
                            ->first();
                    }

                    if (!$account) {
                        $account = Account::where('client_id', $profileId)
                            ->where('account_type', 'like', trim($accountValue))
                            ->first();
                    }
                }

                if (!$account) {
                    $skippedRows[] = "Row {$rowCount}: Account '{$accountValue}' not found for this client.";
                    continue;
                }

                if ($categoryIndex !== null && isset($row[$categoryIndex])) {
                    $categoryValue = trim($row[$categoryIndex]);
                    if ($categoryValue !== '') {
                        if (is_numeric($categoryValue)) {
                            $categoryExists = Category::find((int)$categoryValue);
                            if ($categoryExists) {
                                $categoryId = $categoryExists->id;
                            }
                        } else {
                            $category = Category::firstOrCreate(
                                ['category_name' => $categoryValue],
                                ['user_id' => Auth::id()]
                            );
                            $categoryId = $category->id;
                        }
                    }
                }

                if ($descriptionIndex !== null && isset($row[$descriptionIndex])) {
                    $description = trim($row[$descriptionIndex]);
                }

                if ($typeIndex !== null && isset($row[$typeIndex])) {
                    $typeValue = strtolower(trim($row[$typeIndex]));
                    if (in_array($typeValue, ['expense', 'withdrawal', 'debit', 'spent', 'out'])) {
                        $type = 'Expense';
                    } elseif (in_array($typeValue, ['income', 'deposit', 'credit', 'in'])) {
                        $type = 'Income';
                    } elseif ($typeValue !== '') {
                        $type = ucfirst($typeValue);
                    }
                }

                if ($amountIndex !== null && isset($row[$amountIndex])) {
                    $amountValue = $row[$amountIndex];
                    $normalizedAmount = str_replace(['$', '₱', ',', ' '], '', $amountValue);
                    $isNegative = str_contains($normalizedAmount, '(') || str_contains($normalizedAmount, '-') && $normalizedAmount[0] === '-';
                    $normalizedAmount = str_replace(['(', ')', '-'], '', $normalizedAmount);
                    $amount = (float)$normalizedAmount;
                    if ($isNegative) {
                        $amount = -abs($amount);
                    }
                }

                if ($dateIndex !== null && isset($row[$dateIndex]) && trim($row[$dateIndex]) !== '') {
                    try {
                        $date = Carbon::parse($row[$dateIndex])->toDateTimeString();
                    } catch (\Exception $e) {
                        $date = null;
                    }
                }

                if ($updatedDateIndex !== null && isset($row[$updatedDateIndex]) && trim($row[$updatedDateIndex]) !== '') {
                    try {
                        $updatedAt = Carbon::parse($row[$updatedDateIndex])->toDateTimeString();
                    } catch (\Exception $e) {
                        $updatedAt = null;
                    }
                }

                if (!$date) {
                    $date = now()->toDateTimeString();
                }

                if (!$updatedAt) {
                    $updatedAt = $date;
                }

                $transAmount = $amount;
                if (strtolower($type) === 'expense') {
                    $transAmount = -abs($amount);
                } elseif (strtolower($type) === 'income') {
                    $transAmount = abs($amount);
                } elseif ($amount < 0) {
                    $transAmount = $amount;
                    $type = 'Expense';
                } else {
                    $transAmount = $amount;
                    $type = 'Income';
                }

                if ($amount === 0) {
                    continue;
                }

                $transaction = FinancialTrans::create([
                    'account_id' => $account->id,
                    'category_id' => $categoryId,
                    'type' => $type,
                    'description' => $description,
                    'amount' => $transAmount,
                    'created_at' => $date,
                    'updated_at' => $updatedAt,
                ]);

                $importedTransactions[] = $transaction->id;
            }

            fclose($handle);

            if (!empty($importedTransactions)) {
                $import = TransactionImport::create([
                    'client_id' => $client->id,
                    'staff_id' => Auth::id(),
                    'import_data' => [
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'header' => $header,
                        'total_rows' => $rowCount,
                        'imported_count' => count($importedTransactions),
                        'skipped_count' => count($skippedRows),
                        'skipped_reasons' => array_slice($skippedRows, 0, 10),
                    ],
                    'transaction_ids' => $importedTransactions,
                    'status' => 'completed',
                ]);

                $accountIds = Account::where('client_id', $profileId)->pluck('id');
                foreach ($accountIds as $accountId) {
                    $this->recalculateAccountBalance($accountId);
                }

                session()->put('last_import_id', $import->id);

                $message = count($importedTransactions) . ' transactions imported from "' . $file->getClientOriginalName() . '"';
                if (count($skippedRows) > 0) {
                    $message .= ' (' . count($skippedRows) . ' rows skipped)';
                }

                return redirect()->back()->with('success', $message);
            }

            $errorMessage = 'No valid transactions found in the CSV file.';
            if (!empty($errors)) {
                $errorMessage = implode(' ', $errors);
            } elseif (!empty($skippedRows)) {
                $availableAccounts = Account::where('client_id', $profileId)->pluck('account_type')->join(', ');
                $errorMessage = 'All ' . $rowCount . ' rows skipped. ' . implode('; ', array_slice($skippedRows, 0, 3));
                if (!empty($availableAccounts)) {
                    $errorMessage .= ' Available accounts: ' . $availableAccounts;
                }
            }

            return redirect()->back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing CSV: ' . $e->getMessage());
        }
    }

    /**
     * Undo the last import
     */
    public function undoImport(User $client, $importId)
    {
        if (!$this->getAssignedClientUserIds()->contains($client->id)) {
            abort(403, 'Unauthorized.');
        }

        $clientProfile = $client->clientProfile;
        if (!$clientProfile) {
            abort(404, 'Client profile not found.');
        }

        $import = TransactionImport::findOrFail($importId);
        
        if ($import->client_id != $client->id || $import->status !== 'completed') {
            return redirect()->back()->with('error', 'Cannot undo this import.');
        }

        FinancialTrans::whereIn('id', $import->transaction_ids)->delete();
        $import->update(['status' => 'undone']);

        $accountIds = Account::where('client_id', $clientProfile->id)->pluck('id');
        foreach ($accountIds as $accountId) {
            $this->recalculateAccountBalance($accountId);
        }

        return redirect()->back()->with('success', 'Import undone successfully!');
    }

    /**
     * Redo the last undone import
     */
    public function redoImport(User $client, $importId)
    {
        if (!$this->getAssignedClientUserIds()->contains($client->id)) {
            abort(403, 'Unauthorized.');
        }

        $clientProfile = $client->clientProfile;
        if (!$clientProfile) {
            abort(404, 'Client profile not found.');
        }

        $import = TransactionImport::findOrFail($importId);
        
        if ($import->client_id != $client->id || $import->status !== 'undone') {
            return redirect()->back()->with('error', 'Cannot redo this import.');
        }

        FinancialTrans::whereIn('id', $import->transaction_ids)->restore();
        $import->update(['status' => 'completed']);

        $accountIds = Account::where('client_id', $clientProfile->id)->pluck('id');
        foreach ($accountIds as $accountId) {
            $this->recalculateAccountBalance($accountId);
        }

        return redirect()->back()->with('success', 'Import redone successfully!');
    }

    /**
     * Delete an import and all its transactions permanently
     */
    public function deleteImport(User $client, $importId)
    {
        if (!$this->getAssignedClientUserIds()->contains($client->id)) {
            abort(403, 'Unauthorized.');
        }

        $clientProfile = $client->clientProfile;
        if (!$clientProfile) {
            abort(404, 'Client profile not found.');
        }

        $import = TransactionImport::findOrFail($importId);
        
        if ($import->client_id != $client->id) {
            return redirect()->back()->with('error', 'Cannot delete this import.');
        }

        $fileName = $import->import_data['file_name'] ?? 'Unknown file';
        $transactionCount = count($import->transaction_ids ?? []);

        // Permanently delete all imported transactions
        FinancialTrans::whereIn('id', $import->transaction_ids)->forceDelete();
        
        // Delete the import record
        $import->forceDelete();

        // Recalculate account balances
        $accountIds = Account::where('client_id', $clientProfile->id)->pluck('id');
        foreach ($accountIds as $accountId) {
            $this->recalculateAccountBalance($accountId);
        }

        return redirect()->back()->with('success', "Import from \"{$fileName}\" ($transactionCount transactions) permanently deleted!");
    }
}
