<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\EmailConfig;
use App\Models\EndUser\TPUser;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $applications = $this->getAllApplicationByStatus($user->id);
        return view('enduser.dashboard', $applications);
    }


    public function blacklistedCheck($tp_name, $tp_phone, $tp_email)
    {
        // $blacklisted=blacklist::where('status','blacklisted')->get();
        // foreach($blacklisted as $blacklist){
        //     if($tp_name==$blacklist->tp_name){
        //         return true;
        //     }else if($tp_phone==$blacklist->tp_phone){
        //         return true;
        //     }else if($tp_email==$blacklist->tp_email){
        //         return true;
        //     }
        // }
        // return false;
    }

    public function sendEmail($email_to, $email_for, $extra = [])
    {
        // $to=$email_to;

        // $emailData=EmailConfig::where('email_for',$email_for)->first();

        // $from   =$emailData->email_from;
        // $subject =$emailData->subject;
        // $message =$emailData->message;

        // if($email_for == "registration"){
        //     $message .=" Your Registration Id is: ".$extra['tpDId'];
        // }else if($email_for == "reset_password"){
        //     $message .= $extra['otp'];
        // }

        // $headers = "MIME-Version: 1.0" . "\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // $headers .= 'From: ' . $from . "\r\n" .
        //         'Reply-To: ' . $from . "\r\n" ;

        // if (!$from) {
        //     $from = 'noreply@example.com';
        // }
        // return mail($to, $subject, $message, $headers);
    }

    public function getAllApplicationByStatus($userId = '', $fields = '*')
    {
        $query = TPUser::query()->select($fields);

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        // Fetch all records for this TP user
        $allApplications = $query->get();

        // Categorize results based on conditions
        $summitPendingApplication = $allApplications->where('status', 'Pending')->values();

        $approvedApplication = $allApplications
            ->where('status', 'approved')
            ->filter(fn($app) => strtotime($app->date_of_acc . ' +1 year') > time())
            ->values();

        $summitDisabledApplication = $allApplications
            ->where('status', 'approved')
            ->filter(fn($app) => strtotime($app->date_of_acc . ' +1 year') < time())
            ->values();

        $savedApplication = $allApplications->where('status', 'Saved')->values();

        $summitRejectedApplication = $allApplications->where('status', 'Rejected')->values();
        // dd($allApplications, $summitAcceptedApplication, $summitPendingApplication, $summitDisabledApplication, $nonSummitApplication, $summitRejectedApplication);
        return [
            "summitAcceptedApplication"  => $approvedApplication ?? collect(),
            "summitPendingApplication"   => $summitPendingApplication ?? collect(),
            "nonSummitApplication"       => $savedApplication ?? collect(),
            "summitRejectedApplication"  => $summitRejectedApplication ?? collect(),
            "summitDisabledApplication"  => $summitDisabledApplication ?? collect(),
        ];
    }


}
