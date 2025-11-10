<?php

namespace App\Models\Admin;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
     protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'break',
        'working_hours'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
