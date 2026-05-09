<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class);
    }

    /**
     * Get assignments where this user is the Staff.
     * Links: User -> StaffProfile -> StaffAssignment
     */
    public function staffAssignments(): HasManyThrough
    {
        return $this->hasManyThrough(
            StaffAssignment::class,
            StaffProfile::class,
            'user_id',   // Foreign key on staff_profiles
            'staff_id',  // Foreign key on staff_assignments
            'id',        // Local key on users
            'id'         // Local key on staff_profiles
        );
    }

    public function assignedStaff()
    {
        // For eager loading: use client_id directly
        // This is used by ->with('assignedStaff') in queries
        return $this->hasMany(StaffAssignment::class, 'client_id');
    }

    /**
     * Get the first assigned staff member (for single access in views)
     */
    public function getFirstAssignedStaffAttribute()
    {
        if (!$this->clientProfile) {
            return null;
        }
        return StaffAssignment::where('client_id', $this->clientProfile->id)
            ->with('staffProfile.user')
            ->first();
    }

    public function assignedClients()
    {
        return $this->staffAssignments()->with('clientProfile.user');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function accounts(): HasMany
    {
        // For eager loading: use client_id directly (works when clientProfile is loaded)
        // This is used by ->with('accounts') in queries
        return $this->hasMany(Account::class, 'client_id');
    }

    /**
     * Get accounts through the client profile (client_id in accounts references client_profiles.id)
     * Use this method for direct access to ensure correct profile ID is used
     */
    public function profileAccounts()
    {
        if (!$this->clientProfile) {
            return collect([]);
        }
        return Account::where('client_id', $this->clientProfile->id)->get();
    }

    /**
     * Get accounts - uses profile ID for direct access
     * Use this for $user->clientAccounts to get correct accounts
     */
    public function clientAccounts()
    {
        if (!$this->clientProfile) {
            return collect([]);
        }
        return Account::where('client_id', $this->clientProfile->id)->get();
    }

}
