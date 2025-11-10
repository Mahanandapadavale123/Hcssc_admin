<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MasterEquipment extends Model
{
    //

    use HasFactory;

    protected $table = 'master_equipment';

    protected $fillable = [
        'qual_code',
        'equipmentName',
        'quantityRequired',
        'status',
            ];

}
