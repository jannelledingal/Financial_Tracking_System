<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class StaffAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', // This is a StaffProfile ID
        'client_id', // This is a ClientProfile ID
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Relationship to the Staff Profile
     */
    public function staffProfile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    /**
     * Relationship to the Client Profile
     */
    public function clientProfile(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class, 'client_id');
    }

    /**
     * Corrected Helper to get the Staff User directly.
     * This uses HasOneThrough to bridge: StaffAssignment -> StaffProfile -> User
     */
    public function staffUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,         // The final model we want (the consultant)
            StaffProfile::class, // The intermediate model (the profile)
            'id',                // Foreign key on StaffProfile (matches staff_id in this table)
            'id',                // Foreign key on User (matches user_id in StaffProfile)
            'staff_id',          // Local key on StaffAssignment
            'user_id'            // Local key on StaffProfile
        );
    }
}