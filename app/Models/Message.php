<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    // This line is MANDATORY to allow saving form data
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'attachment_path',
        'attachment_name',
    ];

    // Relationships to help display names in the dashboard
    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}