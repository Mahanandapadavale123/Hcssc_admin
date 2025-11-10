<?php

namespace App\Models\EndUser;

use Illuminate\Database\Eloquent\Model;

class TCenter extends Model
{

    protected $fillable = [ "tp_id", "tc_name", "tc_type", "tc_type_other", "affiliation_name", "validity_start_date","validity_end_date","affiliation_details",
                            "affiliation_remark", "address","nearby_landmark", "pin_code","state","district","city", "longitude","latitude", "plus_code_address",
                            "area_classification", "address_proof_type", "address_proof_document","affiliation_doc", "lang_of_instruction", "lang_of_instruction_other",
                            "total_net_carpet_area", "add_covered_area", ];

    protected $casts = ['lang_of_instruction' => 'array'];



    public function tpUser()
    {
        return $this->belongsTo(TPUser::class, 'tp_id');
    }

}
