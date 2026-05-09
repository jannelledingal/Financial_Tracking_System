<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTrans extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'financial_trans';

    protected $fillable = [
        'account_id',
        'category_id',
        'amount',
        'type',
        'description',
        'voided_by',
        'void_reason',
        'edited_by',
        'edit_reason',
        'original_amount',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Check if transaction is voided
     */
    public function isVoided(): bool
    {
        return $this->deleted_at !== null;
    }

    /**
     * Check if transaction was edited
     */
    public function isEdited(): bool
    {
        return $this->edited_by !== null;
    }
}
