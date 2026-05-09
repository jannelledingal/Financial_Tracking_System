<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create trigger to auto-update balance after insert
        
        DB::unprepared("
            CREATE TRIGGER trg_update_balance_after_insert
            AFTER INSERT ON financial_trans
            FOR EACH ROW
            BEGIN
                IF NEW.account_id IS NOT NULL AND NEW.amount IS NOT NULL THEN
                    IF NEW.type = 'Income' THEN
                        UPDATE accounts SET balance = balance + NEW.amount WHERE id = NEW.account_id;
                    ELSEIF NEW.type = 'Expense' THEN
                        UPDATE accounts SET balance = balance - NEW.amount WHERE id = NEW.account_id;
                    ELSEIF NEW.type = 'Transfer' THEN
                        UPDATE accounts SET balance = balance - NEW.amount WHERE id = NEW.account_id;
                    END IF;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_balance_after_insert');
    }
};