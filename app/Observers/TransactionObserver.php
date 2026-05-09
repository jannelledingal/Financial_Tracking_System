<?php

namespace App\Observers;

use App\Models\FinancialTrans;
use App\Models\Account;
use Exception;

class TransactionObserver
{
    public function creating(FinancialTrans $transaction)
    {
        if ($transaction->type === 'Expense') {
            $account = Account::find($transaction->account_id);
            if ($account && $account->balance < abs($transaction->amount)) {
                throw new Exception('Insufficient balance for this expense');
            }
        }
    }

    public function created(FinancialTrans $transaction)
    {
        $account = Account::find($transaction->account_id);
        if (!$account) {
            return;
        }

        if ($transaction->type === 'Income') {
            $account->increment('balance', abs($transaction->amount));
        } elseif ($transaction->type === 'Expense') {
            $account->decrement('balance', abs($transaction->amount));
        }
    }
}
