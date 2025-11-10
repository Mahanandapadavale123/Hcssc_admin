<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Admin\EndUserCharges;
use App\Models\Admin\MasterQualifications;
use App\Models\EndUser\IndustryBranches;
use App\Models\EndUser\IndustryPartners;
use App\Models\EndUser\TpTrainerAndEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EndUser\TCenter;
use App\Models\EndUser\TcFacilities;
use App\Models\EndUser\TcGalleries;
use App\Models\EndUser\TcQualifications;
use App\Models\EndUser\TcTrainers;
use App\Models\EndUser\TpAuditTurnover;
use App\Models\EndUser\TpIndustryConnection;
use App\Models\EndUser\TpPayments;
use App\Models\EndUser\TpStaff;
use App\Models\EndUser\TpTrainingInfrastructure;
use App\Models\EndUser\TPUser;
use App\Models\payment;
use Exception;
use Log;
use Storage;

class IndustryRegistrationController extends Controller
{

    // Step 1
    public $tpGenDetails, $expFilesTPGenDetails, $tpMngDetails, $tpMngFiles, $inDirectors ,  $inBranches, $inBranchesFiles, $inAffidiateFiles;



    public function __construct() {


        // Step 1
        $this->tpGenDetails = ['tp_name', 'legel_type_of_tp', 'legal_type_other', 'yoe', 'pan', 'gst', 'legal_remark', 'address','pin_code','state','district','city','plus_code_address',
                                'address_proof_type', 'telephone_number', 'fax_no', 'contact_number', 'email', 'website_link' ];
        $this->expFilesTPGenDetails = [ 'legel_type_roof_file',"yoe_proof", "pan_proof", "gst_proof",  'address_proof_file'];

        // Setp 2
        $this->tpMngDetails = [ 'account_no', 'bank_name','ifsc_code' ];
        $this->tpMngFiles = ["bank_proof"];

        // Step 3
        $this->inDirectors  = ["staff_type", "name", "phone", "email", "father_name", "address", "remark"];
        $this->inBranches = ['office_name','address','pin_code','state','district','city','plus_code_address','address_proof_type'];
        $this->inBranchesFiles = ['address_proof_file'];

        // Step 4
         $this->inAffidiateFiles  = ["undertaking_form_file", "affidavit_cod", "affidavit_not_blacklist", "digital_signature"];

    }


    public function registerSteps($formId, $formStage = 'industryGenDetails')
    {
        $applicationId = $formId;
        $user = auth()->user();

        if($formId == 'undefined'){ die("Something went wrong. Please close the brower and login again."); }


        if($formStage == 'finalSubmit'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            return view("enduser/tpuser/applications/finalSubmit");
        }

        if($formStage == 'formPaymentDetails'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $tpUser = TPUser::where('id', $applicationId)->first();
            if(!$tpUser){
                abort(404);
            }

            $currentRole = ($user->roles->first())->name;
            $endUserPayments = EndUserCharges::where('user_type', $currentRole)->where('payment_type', 'initial_payment')->first();

            $payment = TpPayments::where('tp_id',$applicationId)->where('payment_type','initial_payment')->latest()->first();

            return view("enduser/industry/applications/in-payment-details",[
                'formStage'=>$formStage,
                'formId'=> $formId,

                'tpUser'=> $tpUser,
                'tcType'=> $tpUser->legel_type_of_tp,
                'payments'=>$payment,
                'endUserPayments'=>$endUserPayments,
            ]);
        }

        if($formStage == 'industryAffidiate'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $industryData = $this->inAffidiateFiles;

            $tpuser = TPUser::find($formId)->toArray();
            foreach ($industryData as $key) {
                if(array_key_exists($key, $tpuser)){
                    $industryData[$key] = $tpuser[$key];
                }else{
                    $industryData[$key] = "";
                }
            }

            return view("enduser/industry/applications/in-affidavit",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'formStageData'=> $industryData,
            ]);
        }

        if($formStage == 'industryPartners'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $industryPartners = IndustryPartners::where('tp_id', $formId)->get()->toArray();

            return view("enduser/industry/applications/in-partner-deed", [
                'formStage'          => $formStage,
                'formId'             => $formId,
                'industryPartners'   =>$industryPartners,
            ]);
        }

        if($formStage == 'industryMngDetails'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $bankdetails = [];
            $directors = [];
            $branches = [];

            $bankFields = array_merge($this->tpMngDetails, $this->tpMngFiles);
            $directorFields = $this->inDirectors;
            $branchFields = array_merge($this->inBranches, $this->inBranchesFiles);


            $singleSendBack = array_merge($this->tpMngDetails, $this->tpMngFiles);

            $tpdata = TPUser::find($formId);
            if (!$tpdata) {
                abort(404, "TP data not found");
            }
            foreach ($bankFields as $key) { $bankdetails[$key] = $tpdata->$key ?? '';  }

            // Handle partners
            $directorsData = TpStaff::where('tp_id', $formId)->where('staff_type', 'Owner')->get();
            if ($directorsData->isNotEmpty()) {
                foreach ($directorsData as $index => $director) {
                    foreach ($directorFields as $key) {
                        $directors[$index][$key] = $director->$key ?? '';
                    }
                    $directors[$index]['id'] = $director->id;
                }
            }
            $branchesData = IndustryBranches::where('tp_id', $formId)->get();
            if ($branchesData->isNotEmpty()) {
                foreach ($branchesData as $index => $branch) {
                    foreach ($branchFields as $key) {
                        $branches[$index][$key] = $branch->$key ?? '';
                    }
                    $branches[$index]['id'] = $branch->id;
                }
            }

            return view("enduser/industry/applications/in-mng-details", [
                'formStage'       => $formStage,
                'formId'          => $formId,
                'bankdetails'     => $bankdetails,
                'directors'       => $directors,
                'branches'        => $branches,
            ]);
        }

        if($formStage == 'industryGenDetails'){

            if ($formId != 'new') {
                $tpuser = TPUser::find($formId);
                if ($tpuser) {
                    $formData = [];
                    foreach ($this->tpGenDetails as $field) {
                        $formData[$field] = $tpuser->$field ?? '';
                    }
                    foreach ($this->expFilesTPGenDetails as $field) {
                        $formData[$field] = $tpuser->$field ?? '';
                    }
                } else {
                    $sendBackArray = array_flip(array_merge($this->tpGenDetails, $this->expFilesTPGenDetails));
                    foreach ($sendBackArray as $key => $value) {
                        $sendBackArray[$key] = '';
                    }
                    $formData = $sendBackArray;
                }

            } else {
                $sendBackArray = array_flip(array_merge($this->tpGenDetails , $this->expFilesTPGenDetails));
                foreach ($sendBackArray as $key => $value) {
                    $sendBackArray[$key]='';
                }
                $formData = $sendBackArray;
            }
            return view("enduser/industry/applications/in-gen-details",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'formStageData' => $formData,
                'TpAffTcAccGuidelines'=>session('TpAffTcAccGuidelines'),
            ]);
        }

    }


    public function industryFormSubmitSteps($formStage, Request $rq)
    {
        $allGood = false;
        $user = auth()->user();
        $applicationId = $rq->formForm;


        if($formStage == 'formPaymentDetails'){

            DB::beginTransaction();
            try{

                $this->validate($rq,[
                    'payment_method'=> 'required|max:255',
                    'payment_details'=> 'required|max:255',
                    'amount'=> 'required|max:255',
                    'date'=> 'required|max:255',
                ]);

                if(!empty($rq)){
                    $userController = new UserController();
                    $userController->log($rq->formForm,'user','initial_submit', 'Application send to HCSSC');

                    if (TpPayments::where('tp_id',$rq->formForm)->where('payment_type',$rq->type)->exists()) {
                        $updatePaymentItems =$rq->only(['tp_id','payment_method','payment_type','payment_details','payment_date','amount']);
                        $file = $rq->file('payment_file');
                        if(!empty($rq->payment_file)){
                            $fileName =  $user->username ."/initial_payment_file-".date('ymdtms').".".$file->extension();
                            $file->storeAs('public/industryusers/', $fileName);
                            $updatePaymentItems['payment_file']= "industryusers/{$fileName}";
                        }
                        if(TpPayments::where('tp_id',$rq->formForm)->where('payment_type',$rq->type)->update($updatePaymentItems)){
                            if(!empty($rq->undertaking_form)){
                                $file = $rq->file('undertaking_form');
                                $fileName = $user->username . "/undertaking_form-".date('ymdtms').".".$file->extension();
                                $file->storeAs('public/industryusers/', $fileName);
                                TPUser::where('id',$applicationId )->update(['undertaking_form_file'=>  "industryusers/{$fileName}" ]);
                            }
                        };
                    }else{

                        $userController = new UserController();
                        $userController->log($rq->formForm,'user','initial_submit', 'Application send to HCSSC');

                        $payment= new TpPayments();
                        $payment->tp_id = $applicationId;
                        $payment->payment_method = $rq->payment_method ;
                        $payment->payment_type= $rq->type;
                        $payment->payment_details= $rq->payment_details;
                        $payment->payment_date = $rq->date;
                        $payment->amount = $rq->amount;
                        $file = $rq->file('payment_file');

                        if(!empty($file)){
                            $fileName =  $user->username ."/initial_payment_file-".date('ymdtms').".".$file->extension();
                            $file->storeAs('public/industryusers/', $fileName);
                            $payment->payment_file = "industryusers/{$fileName}";
                        }
                        if($payment->save()){
                            if(!empty($rq->undertaking_form)){
                                $file = $rq->file('undertaking_form');
                                $fileName = $user->username ."/undertaking_form-".date('ymdtms').".".$file->extension();
                                $file->storeAs('public/industryusers/', $fileName);
                                TPUser::where('id',$applicationId )->update(['undertaking_form_file'=> "industryusers/{$fileName}" ]);
                            }
                        }
                    }
                }

                DB::commit();
                $allGood = true;
            }catch(Exception $ex){
                DB::rollBack();
                Log::error('TC Room Area Save Failed', [ 'error' => $ex->getMessage(), 'stage' => $formStage, 'request' => $rq->all(),]);
                return back()->with('error', 'Unable to save training centre details. Please try again.');
            }


        }

        if($formStage == 'industryAffidiate'){

            $tp = TPUser::find($applicationId);
            if (!$tp) {
                return response()->json(['error' => 'Industry record not found, Please check again!',], 404);
            }

            DB::beginTransaction();
            try{
                $uploadFields =  $this->inAffidiateFiles;

                foreach ($uploadFields as $field) {
                    if ($rq->hasFile($field)) {
                        $file = $rq->file($field);
                        if (!empty($tp->$field) && Storage::exists('public/' . $tp->$field)) {
                            Storage::delete('public/' . $tp->$field);
                        }

                        $fileName = "industryusers/" . $user->username . "/{$field}-" . now()->format('YmdHis') . "." . $file->extension();
                        $file->storeAs('public', $fileName);
                        $tp->$field = $fileName;
                    }
                }

                $tp->save();

                DB::commit();
                $allGood = true;
            }catch (Exception $e) {
                DB::rollBack();
                Log::error("TC Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                return back()->with('error', 'Unable to save Staff Details. Please try again.');
            }

        }

        if ($formStage == 'industryPartners') {

            DB::beginTransaction();
            try {
                $username = $user->username;

                $partnerNames = $rq->input('party_name', []);

                foreach ($partnerNames as $index => $name) {
                    if (empty($name)) continue;

                    $id = $rq->input("partner_id.{$index}");
                    $data = [
                        'tp_id'          => $applicationId,
                        'party_name'     => $name,
                        'dt_of_partner'  => $rq->input("dt_of_partner.{$index}"),
                        'purpose'        => $rq->input("purpose.{$index}"),
                        'remark'         => $rq->input("remark.{$index}"),
                    ];

                    if (!empty($id)) {
                        $partner = IndustryPartners::find($id);
                        if ($partner) {
                            foreach ($data as $key => $value) {
                                $partner->$key = $value;
                            }
                            $partner->save();
                            continue;
                        }
                    }

                    $partner = new IndustryPartners();
                    foreach ($data as $key => $value) {
                        $partner->$key = $value;
                    }
                    $partner->save();
                }

                DB::commit();
                $allGood = true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("TP Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                return back()->with('error', 'Unable to save Staff Details. Please try again.');
            }
        }

        if ($formStage == 'industryMngDetails') {
            DB::beginTransaction();
            try {
                $username = $user->username;
                $tpuser = TPUser::findOrFail($applicationId);
                $tpuser->exists = true;
                $tpuser->id = $applicationId;

                // Bank Details
                foreach ($this->tpMngDetails as $key) {
                    $tpuser->$key = $rq->input($key, '');
                }

                foreach ($this->tpMngFiles as $fieldName) {
                    if ($rq->hasFile($fieldName)) {
                        $file = $rq->file($fieldName);
                        $fileName = "{$fieldName}-" . now()->format('YmdHis') . '.' . $file->extension();
                        $file->storeAs("public/industryusers/{$username}", $fileName);
                        $tpuser->$fieldName = "industryusers/{$username}/{$fileName}";
                    }
                }
                $tpuser->save();


                $dirIds = $rq->input('dir_id', []);
                $dirNames = $rq->input('dir_name', []);

                foreach ($dirNames as $index => $dirName) {
                    if (empty($dirName)) continue;

                    $id = $dirIds[$index] ?? null;
                    $staff = !empty($id)
                        ? TpStaff::where('id', $id)->where('tp_id', $applicationId)->first()
                        : new TpStaff();

                    if (!$staff) $staff = new TpStaff();

                    $staff->tp_id       = $applicationId;
                    $staff->staff_type  = 'Owner';
                    $staff->name        = $dirName;
                    $staff->father_name = $rq->input("dir_father_name.{$index}");
                    $staff->phone       = $rq->input("dir_phone.{$index}");
                    $staff->email       = $rq->input("dir_email.{$index}");
                    $staff->address     = $rq->input("dir_address.{$index}");
                    $staff->save();
                }

                // === Save Branches ===
                $branchIds = $rq->input('branch_id', []);
                $officeNames = $rq->input('office_name', []);

                foreach ($officeNames as $index => $officeName) {
                if (empty($officeName)) continue;

                    $id = $branchIds[$index] ?? null;
                    $branch = !empty($id)
                        ? IndustryBranches::where('id', $id)->where('tp_id', $applicationId)->first()
                        : new IndustryBranches();

                    if (!$branch) $branch = new IndustryBranches();

                    $branch->tp_id        = $applicationId;
                    $branch->office_name  = $officeName;
                    $branch->address      = $rq->input("address.{$index}");
                    $branch->pin_code     = $rq->input("pincode.{$index}");
                    $branch->state        = $rq->input("state.{$index}");
                    $branch->district     = $rq->input("distict.{$index}");
                    $branch->city         = $rq->input("city.{$index}");
                    $branch->address_proof_type = $rq->input("address_proof_type.{$index}");

                    if ($rq->hasFile("address_proof_file.{$index}")) {
                        $file = $rq->file("address_proof_file.{$index}");
                        $fileName = "branch-{$index}-" . now()->format('YmdHis') . '.' . $file->extension();
                        $file->storeAs("public/industryusers/{$username}", $fileName);
                        $branch->address_proof_file = "industryusers/{$username}/{$fileName}";
                    }

                    $branch->save();
                }

                DB::commit();
                $allGood = true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("TP Management Save Error: " . $e->getMessage() . " Line No - " . $e->getLine());
                dd($e);
                return back()->with('error', 'Unable to save TP Management Details. Please try again.');
            }
        }

        if($formStage == 'industryGenDetails'){
            $request_type = $rq->formForm;
            if($request_type != 'new'){
                $tpuser = TPUser::where('id', $request_type)->where('user_id', $user->id)->firstOrFail();
                $tpuser->exists = true;
            }else{
                $prefix = 'HCSSC/'.date('Y');
                do {
                    $applicationNo = $prefix . '/' . rand(1000, 9999);
                } while (TPUser::where('application_no', $applicationNo)->exists());

                $tpuser = new TPUser();
                $tpuser->user_id = $user->id;
                $tpuser->status = "Saved";
                $tpuser->application_no = $applicationNo;
            }

            foreach ($rq->all() as $key => $value) {
                if (in_array($key, $this->tpGenDetails)) {
                    $tpuser->$key = $value;
                }
            }

            // dd($tpuser);

            foreach ($this->expFilesTPGenDetails as $inputField) {
                if ($rq->hasFile($inputField)) {
                    $file = $rq->file($inputField);
                    $fileName = $inputField . '-' . now()->format('ymdHis') . '.' . $file->extension();
                    $file->storeAs('industryusers/' . $user->username, $fileName, 'public');
                    $tpuser->$inputField = 'industryusers/' . $user->username . '/' . $fileName;
                }
            }
            if($tpuser->save()){
                $allGood = true;
                $applicationId = $tpuser->id ;

            }else{
                die("unable to save data");
            }
        }

        if($allGood){
            if(!empty($rq->formAction)){
                if($rq->formAction == "saveOnly"){
                    return redirect("/industry/application/".  $applicationId  ."/".$formStage);
                }else if($rq->formAction == 'finalSubmit'){
                    $tpuser = new TPUser();
                    $tpuser->exists = true;
                    $tpuser->id = $applicationId ;
                    $tpuser->tp_form_progress = "completed";
                    $tpuser->pre_submit_status = 'submited';
                    $tpuser->status = 'Pending';
                    $tpuser->post_submit_status = 'payment_pending';
                    $tpuser->save();
                    return redirect("/industry/application/". $applicationId ."/".$rq->formAction);
                }else{
                    $formSteps = [ 'industryGenDetails','industryMngDetails','industryPartners', 'industryAffidiate', 'formPaymentDetails','finalSubmit'];
                    $savedStep   = $tpuser->tp_form_progress ?? 'industryGenDetails';
                    $nextStep    = $rq->formAction;
                    $savedIndex  = array_search($savedStep, $formSteps);
                    $nextIndex   = array_search($nextStep, $formSteps);

                    $tpuser = new TPUser();
                    $tpuser->exists = true;
                    $tpuser->id = $applicationId ;

                    if ($nextIndex !== false && $nextIndex > $savedIndex) {
                        $tpuser->tp_form_progress = $nextStep;
                    }
                    $tpuser->save();
                    return redirect("/industry/application/". $applicationId ."/".$rq->formAction);

                }
            }else{
                return redirect("/industry/newApplication");
            }
        }else{
            die("Error Saving the data");
        }
    }

    public function deleteEntry(Request $rq)
    {
        $user = auth()->user();

        if($rq->target == 'IndustryStaff' || $rq->target == "tcStaff"){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('tp_staff')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }

        if($rq->target == 'branches'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('industry_branches')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }

        if($rq->target == 'industryPartner'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('industry_partners')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }

    }

    public function checkFormProgress($applicationId, $formStage)
    {
        $tpUser = TPUser::find($applicationId);
        if (!$tpUser) {
            return redirect('industry/application/new/industryGenDetails')->with('error', 'Please start from the beginning.');
        }

        $formSteps = [
            'industryGenDetails',
            'industryMngDetails',
            'industryPartners',
            'industryAffidiate',
            'formPaymentDetails',
            'finalSubmit',
            'completed'
        ];

        $savedStep = $tpUser->tp_form_progress ?? 'tpGenDetails';

        $currentIndex = array_search($formStage, $formSteps);
        $savedIndex   = array_search($savedStep, $formSteps);

        // --- Main Logic ---
        if ($currentIndex === false) {
            abort(404, "Invalid form stage.");
        }

        if (!empty(session()->get('isAppViewOnly')) && session()->get('isAppViewOnly') != "Y"){
            if ($tpUser->tp_form_progress === 'completed') {
                return redirect('/home')->with('error', 'Application already completed.');
            }
        }

        // If user tries to go beyond current progress
        if ($currentIndex > $savedIndex) {
            $redirectStep = $formSteps[$savedIndex] ?? 'tpGenDetails';
            return redirect("/industry/application/{$applicationId}/{$redirectStep}")
                ->with('error', 'Please complete previous steps before continuing.');
        }

        // If same or previous step — allow access
        return true;
    }


}
