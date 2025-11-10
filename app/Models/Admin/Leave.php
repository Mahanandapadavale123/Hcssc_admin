<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Leave extends Model
{
    //
     protected $fillable = [
      'employee_id','leave_type','start_date','end_date','days','reason','status','approved_by'
    ];

    protected $casts = [
      'start_date'=>'date',
      'end_date'=>'date',
    ];

    public function leavetype() { return $this->belongsTo(LeaveType::class,'leave_type'); }
    public function employee() { return $this->belongsTo(User::class,'employee_id'); }
    public function approver() { return $this->belongsTo(User::class,'approved_by'); }
}
