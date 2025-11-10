<?php

namespace App\Models\EndUser;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TPUser extends Model
{
    const STATUS_SAVED = 'Saved';
    const STATUS_PENDING = 'Pending';  // Initial Submit
    const STATUS_CORRECTION_REQUIRED = 'Correction Required';
    const STATUS_RESUBMITTED = 'Resubmitted';
    const STATUS_VERIFIED = 'Verified';
    const STATUS_PAYMENT_DONE = 'Payment Done';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_BLACKLISTED = 'Blacklisted';




    public function user(){
        return $this->belongsTo(User::class,  'user_id');
    }

    public function centers()
    {
        return $this->hasMany(TCenter::class, 'tp_id');
    }



}
