<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'emp_code', 'designation', 'dept_id', 'emp_type', 'date_of_joining',
        'date_of_leaving', 'work_location', 'gender', 'date_of_birth', 'blood_group',
        'marital_status', 'full_address', 'bank_account_no', 'ifsc_code', 'bank_name',
        'pan_no', 'aadhaar_no', 'emp_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }



}
