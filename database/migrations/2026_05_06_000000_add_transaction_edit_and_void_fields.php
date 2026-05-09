<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('financial_trans', function (Blueprint $table) {
            $table->softDeletes(); // For tracking voided transactions
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('edit_reason')->nullable();
            $table->decimal('original_amount', 15, 2)->nullable(); // Track original amount before edit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_trans', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeignIdFor('voided_by');
            $table->dropColumn(['void_reason', 'edited_by', 'edit_reason', 'original_amount']);
        });
    }
};
