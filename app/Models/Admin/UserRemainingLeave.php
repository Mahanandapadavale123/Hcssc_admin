<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class UserRemainingLeave extends Model
{

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'remaining_days',
    ];

    // Relationship: each record belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: each record belongs to one leave type

    public function leave()
    {
        return $this->belongsTo(\App\Models\Admin\Leave::class);
    }
    //
}
