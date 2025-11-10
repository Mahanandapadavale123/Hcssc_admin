<?php

namespace App\Models\EndUser;

use Illuminate\Database\Eloquent\Model;

class TpAuditTurnover extends Model
{
    protected $table = 'tp_audit_turnovers';
    protected $fillable = [
        'id',
        'tp_id',
        'financial_yrs',
        'turn_over',
        'report_file',
    ];
}
