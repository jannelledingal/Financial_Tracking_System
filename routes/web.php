<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Staff\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/staff', [UserManagementController::class, 'storeStaff'])->name('admin.users.staff.store');
    Route::get('/admin/users/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::patch('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/staff-profiles', [PageController::class, 'staffProfiles'])->name('admin.staff-profiles');
    Route::get('/admin/client-profiles', [PageController::class, 'clientProfiles'])->name('admin.client-profiles');
    Route::get('/admin/suspended-users', [PageController::class, 'suspendedUsers'])->name('admin.suspended-users');
    Route::get('/admin/role-permissions', [PageController::class, 'rolePermissions'])->name('admin.role-permissions');
    Route::get('/admin/staff-assignments', [PageController::class, 'staffAssignments'])->name('admin.staff-assignments');
    Route::post('/admin/staff-assignments', [UserManagementController::class, 'assignStaff'])->name('admin.staff-assignments.store');
    Route::delete('/admin/staff-assignments/{assignment}', [UserManagementController::class, 'unassignStaff'])->name('admin.staff-assignments.destroy');
    Route::get('/admin/audit-log', [PageController::class, 'auditLog'])->name('admin.audit-log');
    Route::get('/admin/all-transactions', [PageController::class, 'allTransactions'])->name('admin.all-transactions');
    Route::get('/admin/reports', [PageController::class, 'reports'])->name('admin.reports');
    Route::get('/admin/system-settings', [PageController::class, 'systemSettings'])->name('admin.system-settings');

    Route::middleware(['staff'])->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');
        Route::get('/staff/clients', [StaffController::class, 'clients'])->name('staff.clients');
        Route::get('/staff/clients/{client}', [StaffController::class, 'showClient'])->name('staff.show-client');
        Route::post('/staff/deposit', [StaffController::class, 'deposit'])->name('staff.deposit');
        Route::post('/staff/withdraw', [StaffController::class, 'withdraw'])->name('staff.withdraw');
        Route::get('/transactions/export/{client}', [StaffController::class, 'exportTransactions'])->name('transactions.export');
        Route::post('/transactions/import/{client}', [StaffController::class, 'importTransactions'])->name('transactions.import');
        Route::post('/transactions/{client}/undo/{importId}', [StaffController::class, 'undoImport'])->name('transactions.undo');
        Route::post('/transactions/{client}/redo/{importId}', [StaffController::class, 'redoImport'])->name('transactions.redo');
        Route::post('/transactions/{client}/delete/{importId}', [StaffController::class, 'deleteImport'])->name('transactions.delete');
        Route::get('/staff/add-transaction', [StaffController::class, 'addTransaction'])->name('staff.add-transaction');
        Route::post('/staff/add-transaction', [StaffController::class, 'storeTransaction'])->name('staff.add-transaction.store');
        Route::get('/staff/transaction-log', [StaffController::class, 'transactionLog'])->name('staff.transaction-log');
        Route::get('/transactions/{transaction}/edit', [StaffController::class, 'editTransaction'])->name('transactions.edit');
        Route::patch('/transactions/{transaction}', [StaffController::class, 'updateTransaction'])->name('transactions.update');
        Route::get('/transactions/{transaction}/void', [StaffController::class, 'showVoidTransaction'])->name('transactions.void.form');
        Route::post('/transactions/{transaction}/void', [StaffController::class, 'voidTransaction'])->name('transactions.void');
        Route::post('/transactions/sync', [StaffController::class, 'syncTransactions'])->name('transactions.sync');
        Route::post('/transactions/sync-all', [StaffController::class, 'syncAllTransactions'])->name('transactions.sync-all');
        Route::get('/staff/income-records', [StaffController::class, 'incomeRecords'])->name('staff.income-records');
        Route::get('/staff/expense-records', [StaffController::class, 'expenseRecords'])->name('staff.expense-records');
        Route::get('/staff/generate-report', [StaffController::class, 'generateReport'])->name('staff.generate-report');
        Route::get('/staff/client-messages', [StaffController::class, 'clientMessages'])->name('staff.client-messages');
        Route::post('/staff/client-messages', [StaffController::class, 'sendMessage'])->name('staff.client-messages.send');
        Route::get('/staff/client-messages/{clientId}', [StaffController::class, 'getConversation'])->name('staff.client-messages.conversation');
        Route::get('/staff/messages/download/{message}', [StaffController::class, 'downloadAttachment'])->name('messages.download');
        Route::get('/staff/profile', [StaffController::class, 'profile'])->name('staff.profile');
        Route::post('/staff/accounts', [StaffController::class, 'storeAccount'])->name('staff.accounts.store');
        Route::delete('/staff/accounts/{account}', [StaffController::class, 'destroyAccount'])->name('staff.accounts.destroy');
        Route::get('/staff/accounts/{account}/edit', [StaffController::class, 'editAccount'])->name('staff.accounts.edit');
        Route::patch('/staff/accounts/{account}', [StaffController::class, 'updateAccount'])->name('staff.accounts.update');
        Route::get('/staff/reports', [StaffController::class, 'reports'])->name('staff.reports');
    });

    Route::middleware(['client'])->group(function () {
        Route::get('/client', [ClientController::class, 'dashboard'])->name('client.dashboard');
        Route::get('/client/income', [ClientController::class, 'income'])->name('client.income');
        Route::get('/client/expenses', [ClientController::class, 'expenses'])->name('client.expenses');
        Route::get('/client/accounts', [ClientController::class, 'accounts'])->name('client.accounts');
        Route::get('/client/goals', [ClientController::class, 'goals'])->name('client.goals');
        Route::get('/client/statements', [ClientController::class, 'statements'])->name('client.statements');
        Route::get('/client/transaction-history', [ClientController::class, 'transactionHistory'])->name('client.transaction-history');
        Route::get('/client/messages', [ClientController::class, 'messages'])->name('client.messages');
        Route::post('/client/messages/send', [ClientController::class, 'sendMessage'])->name('client.messages.send');
        Route::get('/client/profile', [ClientController::class, 'profile'])->name('client.profile');
    });

    Route::middleware(['client'])->group(function () {
        Route::get('/client', [ClientController::class, 'dashboard'])->name('client.dashboard');
        Route::get('/client/income', [ClientController::class, 'income'])->name('client.income');
        Route::get('/client/expenses', [ClientController::class, 'expenses'])->name('client.expenses');
        Route::get('/client/accounts', [ClientController::class, 'accounts'])->name('client.accounts');
        Route::get('/client/goals', [ClientController::class, 'goals'])->name('client.goals');
        Route::get('/client/statements', [ClientController::class, 'statements'])->name('client.statements');
        Route::get('/client/transaction-history', [ClientController::class, 'transactionHistory'])->name('client.transaction-history');
        Route::get('/client/messages', [ClientController::class, 'messages'])->name('client.messages');
        Route::post('/client/messages/send', [ClientController::class, 'sendMessage'])->name('client.messages.send');
        Route::get('/client/profile', [ClientController::class, 'profile'])->name('client.profile');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
