<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
protected $fillable = ['title', 'date', 'day', 'description', 'is_public', 'status'];
 protected $casts = [
        'date' => 'date',
    ];
}
