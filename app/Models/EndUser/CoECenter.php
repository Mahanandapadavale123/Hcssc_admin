<?php

namespace App\Models\EndUser;

use Illuminate\Database\Eloquent\Model;

class CoECenter extends Model
{

    protected $fillable = [ "tp_id" , "lang_of_instruction", "lang_of_instruction_other", "total_net_carpet_area", "add_covered_area"];

    protected $casts = ['lang_of_instruction' => 'array'];

}
