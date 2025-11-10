<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\EndUser\TPUser;
use App\Models\UserLog;

class UserController extends Controller
{

    public function log($tp_id,$log_type,$log_data, $logRemark) {
        $logIdValue=TPUser::where('id',$tp_id)->first();

        $log = new UserLog();
        $log->user_id = $logIdValue->user_id;
        $log->tp_id = $logIdValue->id;
        $log->log_type = $log_type;
        $log->log_data = $log_data;
        $log->log_remark = $logRemark;
        $log->save();

    }


}
