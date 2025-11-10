<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndUserCharges extends Model
{
    use HasFactory;

    protected $table = 'end_user_charges';

    protected $fillable = [
        'user_type',
        'payment_type',
        'category',
        'description',
        'amount',
        'status',
    ];
}
