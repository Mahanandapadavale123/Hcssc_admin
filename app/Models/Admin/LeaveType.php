<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
   protected $fillable = ['name', 'total_days','status'];
}
