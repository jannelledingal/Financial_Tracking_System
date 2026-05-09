<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\FinancialTrans;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'account_type',
        'balance',
        'currency',
        'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTrans::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTrans::class, 'account_id'); 
    }
}
