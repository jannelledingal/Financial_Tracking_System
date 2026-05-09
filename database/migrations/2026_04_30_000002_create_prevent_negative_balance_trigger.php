<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create trigger to prevent negative balance on expense
        
        DB::unprepared("
            CREATE TRIGGER trg_prevent_negative_balance
            BEFORE INSERT ON financial_trans
            FOR EACH ROW
            BEGIN
                DECLARE current_bal DECIMAL(15,2);
                
                IF NEW.type = 'Expense' THEN
                    IF NEW.account_id IS NOT NULL AND NEW.amount IS NOT NULL THEN
                        SELECT COALESCE(balance, 0) INTO current_bal FROM accounts WHERE id = NEW.account_id;
                        IF current_bal < NEW.amount THEN
                            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insufficient balance for this expense';
                        END IF;
                    END IF;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_prevent_negative_balance');
    }
};