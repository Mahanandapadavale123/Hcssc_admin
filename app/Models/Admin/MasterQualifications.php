<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class MasterQualifications extends Model
{
    //
     use HasFactory;

    protected $table = 'master_qualifications'; // 👈 optional but good practice

    protected $fillable = [
        'mq_name',
        'mq_code',
        'mq_sub_section',
        'status',
    ];
}
