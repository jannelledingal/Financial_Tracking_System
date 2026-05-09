<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\FinancialTrans;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function dashboard(Request $request): View
    {
        $client = Auth::user();
        // Use profileAccounts() to get accounts using the correct profile ID
        $accounts = $client->profileAccounts();
        $accountIds = $accounts->pluck('id');

        $period = $request->get('period', 'year');
        $customDate = $request->get('custom_date');

        // Determine labels based on period
        if ($customDate) {
            $labels = [$customDate];
            $chartTitle = $customDate;
        } elseif ($period == 'today') {
            $labels = ['Morning', 'Afternoon', 'Evening'];
            $chartTitle = 'Today';
        } elseif ($period == 'week') {
            $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $chartTitle = 'This Week';
        } elseif ($period == 'month') {
            $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            $chartTitle = 'This Month';
        } else {
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $chartTitle = now()->year;
        }

        // Build query - Start with Query Builder
        $query = FinancialTrans::with(['account', 'category']);

        // If no accounts exist, force the query to return empty results instead of returning a Collection
        if ($accountIds->isEmpty()) {
            $query->whereRaw('1 = 0'); 
        } else {
            $query->whereIn('account_id', $accountIds);
        }

        // Apply Date Filters (These work because $query is a Query Builder)
        if ($customDate) {
            $query->whereDate('created_at', $customDate);
        } elseif ($period == 'today') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($period == 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } else {
            $query->whereYear('created_at', now()->year);
        }

        $transactions = $query->orderByDesc('created_at')->get();

        $incomeTotal = $transactions->where('type', 'Income')->sum('amount');
        $expenseTotal = $transactions->where('type', 'Expense')->sum('amount');
        $balanceTotal = $accounts->sum('balance');

        // Calculate chart data based on period
        if ($customDate) {
            $monthlyIncome = [(int) $transactions->where('type', 'Income')->sum('amount')];
            $monthlyExpense = [(int) $transactions->where('type', 'Expense')->sum('amount')];
        } elseif ($period == 'today') {
            $monthlyIncome = [0, 0, 0];
            $monthlyExpense = [0, 0, 0];
            foreach ($transactions as $trans) {
                $hour = (int) $trans->created_at->format('H');
                $idx = $hour < 12 ? 0 : ($hour < 18 ? 1 : 2);
                if ($trans->type === 'Income') { $monthlyIncome[$idx] += (int) $trans->amount; } 
                else { $monthlyExpense[$idx] += (int) $trans->amount; }
            }
        } elseif ($period == 'week') {
            $monthlyIncome = array_fill(0, 7, 0);
            $monthlyExpense = array_fill(0, 7, 0);
            foreach ($transactions as $trans) {
                $dayIdx = (int) $trans->created_at->format('N') - 1;
                if ($trans->type === 'Income') { $monthlyIncome[$dayIdx] += (int) $trans->amount; } 
                else { $monthlyExpense[$dayIdx] += (int) $trans->amount; }
            }
        } elseif ($period == 'month') {
            $monthlyIncome = [0, 0, 0, 0];
            $monthlyExpense = [0, 0, 0, 0];
            foreach ($transactions as $trans) {
                $day = (int) $trans->created_at->format('j');
                $weekIdx = min(3, (int) (($day - 1) / 7));
                if ($trans->type === 'Income') { $monthlyIncome[$weekIdx] += (int) $trans->amount; } 
                else { $monthlyExpense[$weekIdx] += (int) $trans->amount; }
            }
        } else {
            // Yearly Logic Fix
            if ($accountIds->isEmpty()) {
                $monthlyTotals = collect();
            } else {
                $monthlyTotals = FinancialTrans::selectRaw('MONTH(created_at) as month, type, SUM(amount) as total')
                    ->whereIn('account_id', $accountIds)
                    ->whereYear('created_at', now()->year)
                    ->groupBy('month', 'type')
                    ->get();
            }

            $monthlyIncome = array_fill(1, 12, 0);
            $monthlyExpense = array_fill(1, 12, 0);

            foreach ($monthlyTotals as $row) {
                if ($row->type === 'Income') {
                    $monthlyIncome[(int) $row->month] = (int) $row->total;
                } else {
                    $monthlyExpense[(int) $row->month] = (int) $row->total;
                }
            }
            $monthlyIncome = array_values($monthlyIncome);
            $monthlyExpense = array_values($monthlyExpense);
        }

        // Apply date filters to the Spending by Category query
$spendingQuery = FinancialTrans::with('category')
    ->selectRaw('category_id, SUM(amount) as total')
    ->whereIn('account_id', $accountIds)
    ->where('type', 'Expense');

        // Add the same logic used for $query above
        if ($customDate) {
            $spendingQuery->whereDate('created_at', $customDate);
        } elseif ($period == 'today') {
            $spendingQuery->whereDate('created_at', now()->toDateString());
        } elseif ($period == 'week') {
            $spendingQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $spendingQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } else {
            $spendingQuery->whereYear('created_at', now()->year);
        }

        $spendingByCategory = $accountIds->isEmpty()
            ? collect()
            : $spendingQuery->groupBy('category_id')
                ->get()
                ->map(function ($row) {
                    return [
                        'name' => $row->category?->category_name ?? 'Misc',
                        'amount' => abs($row->total), // Use abs() to ensure positive numbers for the chart
                    ];
                });


        $assignedStaff = $client->assignedStaff()->get();
        $recentTransactions = $transactions->take(5);
        $recentMessages = Message::with('sender')
            ->where('receiver_id', $client->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('client.dashboard', [
            'client' => $client,
            'accounts' => $accounts,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => abs($expenseTotal),
            'balanceTotal' => $balanceTotal,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'labels' => $labels,
            'chartTitle' => $chartTitle,
            'spendingByCategory' => $spendingByCategory,
            'assignedStaff' => $assignedStaff,
            'recentTransactions' => $recentTransactions,
            'recentMessages' => $recentMessages,
        ]);
    }

    public function income(): View
    {
        $client = Auth::user();
        $accountIds = $client->profileAccounts()->pluck('id');

        $transactions = FinancialTrans::with(['account', 'category'])
            ->whereIn('account_id', $accountIds)
            ->where('type', 'Income')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('client.income', [
            'client' => $client,
            'transactions' => $transactions,
            'totalIncome' => $transactions->sum('amount'),
        ]);
    }

    public function expenses(): View
    {
        $client = Auth::user();
        $accountIds = $client->profileAccounts()->pluck('id');

        $transactions = FinancialTrans::with(['account', 'category'])
            ->whereIn('account_id', $accountIds)
            ->where('type', 'Expense')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('client.expenses', [
            'client' => $client,
            'transactions' => $transactions,
            'totalExpense' => abs($transactions->sum('amount')),
        ]);
    }

    public function accounts(): View
    {
        $client = Auth::user();
        
        // Get accounts using profile ID directly with transaction count
        $accounts = \App\Models\Account::where('client_id', $client->clientProfile->id)
            ->withCount('transactions')
            ->get();

        return view('client.accounts', [
            'client' => $client,
            'accounts' => $accounts,
        ]);
    }

    public function goals(): View
    {
        $client = Auth::user();

        $goals = [
            ['title' => 'Emergency fund', 'current' => 64000, 'target' => 80000, 'color' => 'emerald'],
            ['title' => 'Vacation savings', 'current' => 22000, 'target' => 55000, 'color' => 'sky'],
            ['title' => 'New laptop', 'current' => 28000, 'target' => 58000, 'color' => 'violet'],
            ['title' => 'Down payment', 'current' => 15000, 'target' => 120000, 'color' => 'amber'],
        ];

        return view('client.goals', [
            'client' => $client,
            'goals' => $goals,
        ]);
    }

    public function statements(): View
    {
        $client = Auth::user();

        $statements = [
            ['period' => 'April 2024', 'status' => 'Ready', 'balance' => 322100, 'link' => '#'],
            ['period' => 'March 2024', 'status' => 'Downloaded', 'balance' => 317400, 'link' => '#'],
            ['period' => 'February 2024', 'status' => 'Ready', 'balance' => 309500, 'link' => '#'],
        ];

        return view('client.statements', [
            'client' => $client,
            'statements' => $statements,
        ]);
    }

    public function transactionHistory(): View
    {
        $client = Auth::user();
        $accountIds = $client->profileAccounts()->pluck('id');

        $transactions = FinancialTrans::with(['account.user', 'category'])
            ->whereIn('account_id', $accountIds)
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('client.transaction-history', [
            'client' => $client,
            'transactions' => $transactions,
        ]);
    }

    public function messages(): View
    {
        $client = Auth::user();
        $assignedStaff = $client->assignedStaff()->first();

        $messages = Message::with('sender')
            ->where(function ($query) use ($client) {
                $query->where('sender_id', $client->id)
                      ->orWhere('receiver_id', $client->id);
            })
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return view('client.messages', [
            'client' => $client,
            'messages' => $messages,
            'assignedStaff' => $assignedStaff,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'body' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,csv,xlsx,jpg,png,jpeg,gif,doc,docx,txt|max:5120',
        ]);

        $client = Auth::user();
        
        // Load the assignment AND the associated staff profile with its user
        $assignment = $client->assignedStaff()->with('staffProfile')->first();

        if (!$assignment || !$assignment->staffProfile) {
            return back()->with('error', 'No consultant assigned to your profile.');
        }

        // GET THE ACTUAL USER ID of the staff member
        $staffUserId = $assignment->staffProfile->user_id;

        $message = new Message();
        $message->sender_id = $client->id;
        $message->receiver_id = $staffUserId; // Fix: Use the User ID, not the assignment ID
        $message->content = $request->body;
        $message->is_read = false;

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $message->attachment_path = $path;
        }

        $message->save();

        return back()->with('success', 'Message sent successfully!');
    }

    public function profile(): View
    {
        $client = Auth::user();
        $assignedStaff = $client->assignedStaff()->get();

        return view('client.profile', [
            'client' => $client,
            'assignedStaff' => $assignedStaff,
        ]);
    }
}