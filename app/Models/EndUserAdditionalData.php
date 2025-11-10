<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EndUserAdditionalData extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'end_user_additional_data';

    protected $fillable = [
        'section_code',
        'section',
        'data',
    ];
}

