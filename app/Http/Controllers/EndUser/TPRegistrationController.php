<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Admin\EndUserCharges;
use App\Models\Admin\MasterQualifications;
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

class TPRegistrationController extends Controller
{

    public $expFilesTPGenDetails, $expFilesTPManagment,  $expTPInfraFields, $tc_infrastructure, $tcIndusConFields, $tcIndusConFiles;

    public  $expTCAuditReportFile, $expTCAuditReportField, $expStaffFiles, $expStaffFields, $expAfilFile, $expAfilField;
    public $expTCFiles, $expTCFields, $expTCMngDetailsFields;

    public $tpGenDetails, $tpMngDetails;
    public $tcGenDetails, $expFilesTCGenDetails;
    public $tpQualification;

    public $expTCFacilityFields, $expTCFacilityCheckBoxFields, $tcFacilityFiles;


    public function __construct() {


        // Step 1
        $this->tpGenDetails = ['tp_name', 'legel_type_of_tp', 'legal_type_other', 'legal_remark', 'mission_objective', 'address', 'nearby_landmark','pin_code','state','district','city','plus_code_address', 'address_proof_type', 'contact_number', 'email', 'website_link' ];
        $this->expFilesTPGenDetails = [ 'legel_type_roof_file', 'address_proof_file', 'outside_front_view','outside_right_view', 'outside_other_image','inside_entrance', 'inside_other_image'];

        // Setp 2
        $this->tpMngDetails = [ 'yoe', 'pan', 'gst', 'account_no', 'bank_name','ifsc_code',  'tp_form_progress','status' ];
        $this->expFilesTPManagment = ["yoe_proof", "pan_proof", "gst_proof", "bank_proof"];
        $this->expTCAuditReportField = ["financial_yrs","turn_over","remark"];
        $this->expTCAuditReportFile = ["report_file"];

        // Step 3
        $this->expStaffFields = ["staff_type", "name", "phone", "email", "alt_phone", "education",  "designation", "experience", "resume", "remark"];
        $this->expStaffFiles = ["resume"];

        // Step 4
        $this->tcGenDetails = ["tc_name", "tc_type", "tc_type_other", "affiliation_name", "validity_start_date","validity_end_date","affiliation_details",  "affiliation_remark", "address","nearby_landmark",
                                "pin_code","state","district","city", "longitude","latitude", "plus_code_address", "area_classification", "address_proof_type"];
        $this->expFilesTCGenDetails = ["address_proof_document","affiliation_doc"];

        // Step 5
        $this->expTPInfraFields = ["lang_of_instruction", "lang_of_instruction_other", "total_net_carpet_area", "add_covered_area"];
        $this->tpQualification = ["qual_name","qual_code","qual_sub_sector","qual_trainee_to_trainer_ratio","qual_associated_classroom","qual_associated_lab","qual_no_of_parallel_batch","qual_trainer_ava"];

        // Setp 8
        $this->expTCFacilityFields = ["bldg_lvls", "const_bldg_struct", "prox_pub_trans", "nearest_station", "approach_road", "internet_speed", "internet_speed_file", "difabled_details", "facility_remarks"];
        $this->expTCFacilityCheckBoxFields = ["security_guards", "biometric_attend", "greenery_surround", "power_backup", "training_centre", "cctv_cam_rec", "drinking_water", "housekeeping_staff", "clean_washrooms",
         "fire_extinguisher", "fire_hose_pipe", "first_aid_kit", "fire_safety_instr", "emergency_numbers", "med_safety_facil", "pantry", "library", "parking", "staff_room", "storehouse"];
        $this->tcFacilityFiles = ["image_outarea", "image_approach_road", "image_tc", "image_tc_front", "image_tc_back", "image_tc_left", "image_tc_right", "image_net_bill", "image_biometric_device",
        "image_classroom", "image_lab", "image_firstaid", "image_fire", "image_water", "image_insecption_card", "image_washroom", "image_reception", "image_placementcell", "image_counselling", "image_library",
         "image_office", "image_pantry", "image_parking"];

        //  Step 9
        $this->tcIndusConFields = ["industry_name", "industry_address", "industry_spoc_name", "industry_phone", "industry_email", "industry_scale", "industry_placement", "industry_remarks", "industry_experts_eng_pro", "curriculum_dev", "job_support", "guest_faculty"];
        $this->tcIndusConFiles = ["industry_experts_eng_pro_file", "curriculum_dev_file", "job_support_file", "guest_faculty_file"];

    }


    public function registerSteps($formId, $formStage = 'tpGenDetails')
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

            return view("enduser/tpuser/applications/tp-payment-details",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'tpUser'=> $tpUser->toArray(),
                'tcType'=> $tpUser->legel_type_of_tp,
                'payments'=>$payment,
                'endUserPayments'=>$endUserPayments,
            ]);


        }

        if($formStage == 'tcIndusCon'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $TpIndustry = TpIndustryConnection::where('tp_id',$applicationId)->get()->toArray();

            return view("enduser/tpuser/applications/tc-indus-con",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'indCons'=> $TpIndustry
            ]);
        }

        if($formStage == 'tcFacility'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;



            $singleSendBack = array_merge( $this->expTCFacilityFields, $this->tcFacilityFiles, $this->expTCFacilityCheckBoxFields );

            $tcFacilities = TcFacilities::where('tp_id',$applicationId)->first();
            $tcGalleries = TcGalleries::where('tp_id',$applicationId)->first();

            if($tcFacilities && $tcGalleries){
                $tcdata = array_merge( $tcFacilities->toArray(), $tcGalleries->toArray() );

                foreach ($singleSendBack as $key) {
                    if(array_key_exists($key, $tcdata)){
                        $sendBackArray[$key] = $tcdata[$key];
                    }else{
                        $sendBackArray[$key] = "";
                    }
                }
            }else{
                foreach ($singleSendBack as $key) {
                        $sendBackArray[$key] = "";
                }
            }

            return view("enduser/tpuser/applications/tc-facility",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'formStageData'=> $sendBackArray,
            ]);
        }

        if($formStage == 'tcQualification'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $qual_id = DB::select('SELECT qual_name FROM `tp_training_infrastructures` WHERE tp_id = '.$applicationId.' GROUP BY qual_name');
            $qual_id = json_decode(json_encode($qual_id), true);

            $eqArray = [];
            $returnData = [];

            $tp_trainer_and_equipment = DB::select('SELECT * FROM `tp_trainer_and_equipment` WHERE tp_id = '.$applicationId);
            if(!empty($tp_trainer_and_equipment)){
                $tp_trainer_and_equipment = json_decode(json_encode($tp_trainer_and_equipment), true);
                foreach ($tp_trainer_and_equipment as $k => $tc_trainer) {
                    $returnData[$tc_trainer['short_qul_code']][$tc_trainer['short_tool_name']]['avlQty'] = $tc_trainer['qty_avl'];
                    $returnData[$tc_trainer['short_qul_code']][$tc_trainer['short_tool_name']]['remark'] = $tc_trainer['remark'];
                }
            }else{
                $returnData=[];
            }

            $tc_trainers = DB::select('SELECT * FROM `tc_trainers` WHERE tp_id = '.$applicationId);
            if(!empty($tc_trainers)){
                $tc_trainers = json_decode(json_encode($tc_trainers), true);

                foreach ($tc_trainers as $k => $tc_trainer) {
                    $rdtc_trainer[$tc_trainer['qCode']]['trainerName'] = $tc_trainer['trainerName'];
                    $rdtc_trainer[$tc_trainer['qCode']]['trainerExp'] = $tc_trainer['trainerExp'];
                    $rdtc_trainer[$tc_trainer['qCode']]['trainer'] = $tc_trainer['trainer'];
                }
            }else{
                $rdtc_trainer=[];
            }

            if(!empty($qual_id)){
                foreach ($qual_id as $v) {

                    $Qname = DB::select('SELECT mq_code, mq_name FROM `master_qualifications` WHERE id = ?',[$v['qual_name']]);
                    $que = DB::select('SELECT equipmentName, quantityRequired FROM `master_equipment` WHERE qual_code = ?',[$Qname[0]->mq_code]);
                    if(!empty($que)){
                        $eqArray[$Qname[0]->mq_name] = json_decode(json_encode($que), true);
                    }else{
                        echo("no equipemnt found fro this qualification. ".$Qname[0]->mq_code."<br>");
                    }
                }
            }else{
                return redirect('tp/application/'.$formId.'/tcRoomArea')->with('error', 'Please add Class/Lab details before save!');
            }

            $tc_qualifications = TcQualifications::where('tp_id', $applicationId)->first();

            return view("enduser/tpuser/applications/tp-qualification",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'eqArray'=> $eqArray,
                'tc_trainer_and_equ'=> $returnData,
                'rdtc_trainer'=> $rdtc_trainer,
                'AllTCQualifications'=> $tc_qualifications,
            ]);
        }

        if($formStage == 'tcRoomArea'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $qualiAll = MasterQualifications::where('status', 'active')->get()->toArray();

            $tcInfra = DB::Select('select '.implode(',',$this->expTPInfraFields).' from t_centers WHERE 1  AND t_centers.tp_id = '.$applicationId);

            $tcClases = TpTrainingInfrastructure::where('tp_id', $formId)->where('type','class')->get()->toArray();
            $tclabs = TpTrainingInfrastructure::where('tp_id', $formId)->where('type','lab')->get()->toArray();
            $tcHybridlabs = TpTrainingInfrastructure::where('tp_id', $formId)->where('type','hybrid')->get()->toArray();

            return view("enduser/tpuser/applications/tc-room-area",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'masterQualifications'=> $qualiAll,
                'tcInfraData' => json_decode(json_encode($tcInfra[0]), true),
                'tcClases'=> $tcClases,
                'tclabs'=> $tclabs,
                'thybridlabs'=> $tcHybridlabs,
            ]);
        }

        if($formStage == 'tcStaffDetails'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            if($formId == 'new'){
                return redirect('tp/application/new/tcGenDetails')->with('error','Please fill this form to move ahead!');
            }
            $tcStaff = TpStaff::where('tp_id', $formId)->where('staff_type','TC')->get()->toArray();
            return view("enduser/tpuser/applications/tc-staff-details",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'tcStaff'=> $tcStaff,
            ]);


        }

        if($formStage == 'tcGenDetails'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;


            $tpData = TPUser::where('id', $applicationId)->first();
            if(!$tpData){
                return redirect('tp/application/new/tcGenDetails')->with('error','Please fill this form to move ahead!');
            }
            $tps = TCenter::where('tp_id', $tpData->id )->first();
            if ($tps) {
                $formData = [];
                foreach ($this->tcGenDetails as $field) {
                    $formData[$field] = $tps->$field ?? '';
                }
                foreach ($this->expFilesTCGenDetails as $field) {
                    $formData[$field] = $tps->$field ?? '';
                }
            } else {
                $sendBackArray = array_flip(array_merge($this->tcGenDetails, $this->expFilesTCGenDetails));
                foreach ($sendBackArray as $key => $value) {
                    $sendBackArray[$key] = '';
                }
                $formData = $sendBackArray;
            }

            return view("enduser/tpuser/applications/tc-gen-details",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'tpData'=>$tpData,
                'formStageData'=> $formData,
            ]);
        }

        if($formStage == 'tpMngStaffDetails'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;


            $tpStaffDir = TpStaff::where('tp_id', $formId)->where('staff_type','Owner')->get()->toArray();
            $tpStaffSPOC = TpStaff::where('tp_id', $formId)->where('staff_type','SPOC')->get()->toArray();
            $tpStaffgen = TpStaff::where('tp_id', $formId)->where('staff_type','Management')->get()->toArray();

            return view("enduser/tpuser/applications/tp-staff-details", [
                'formStage'        => $formStage,
                'formId'           => $formId,
                'tpDirStaffs'       =>$tpStaffDir,
                'tpSPOCs'           =>$tpStaffSPOC,
                'tpStaffGens'       =>$tpStaffgen
            ]);
        }

        if($formStage == 'tpMngDetails'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $sendBackArray = [];
            $multipleSendBackArray = [];

            $singleSendBack = array_merge($this->tpMngDetails, $this->expFilesTPManagment);

            $tpdata = TPUser::find($formId);
            if (!$tpdata) {
                abort(404, "TP data not found");
            }
            foreach ($singleSendBack as $key) { $sendBackArray[$key] = $tpdata->$key ?? '';  }

            // Handle turnover reports
            $turnoverFields = array_merge( $this->expTCAuditReportField, $this->expTCAuditReportFile);
            $tpTurnOverReports = TpAuditTurnover::where('tp_id', $formId)->get();
            if ($tpTurnOverReports->isNotEmpty()) {
                foreach ($tpTurnOverReports as $index => $report) {
                    $multipleSendBackArray['tp_turn_over_report'][$index]['turnoverKey'] = $report->id;

                    foreach ($turnoverFields as $key) {
                        $multipleSendBackArray['tp_turn_over_report'][$index][$key] = $report->$key ?? '';
                    }
                }
            } else {
                $multipleSendBackArray['tp_turn_over_report'] = [];
            }
            return view("enduser/tpuser/applications/tp-mng-details", [
                'formStage'        => $formStage,
                'formId'           => $formId,
                'sendBackArray'    => $sendBackArray,
                'allTurnOver'      => $multipleSendBackArray['tp_turn_over_report'],
            ]);
        }

        if($formStage == 'tpGenDetails'){



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
            return view("enduser/tpuser/applications/tp-gen-details",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'formStageData' => $formData,
                'TpAffTcAccGuidelines'=>session('TpAffTcAccGuidelines'),
            ]);
        }

    }


    public function tpFormSubmitSteps($formStage, Request $rq)
    {
        $allGood = false;
        $user = auth()->user();
        $applicationId = $rq->formForm;


        if($formStage == 'formPaymentDetails'){

            DB::beginTransaction();
            try{
                $tpType=TPUser::where('id', $applicationId)->value('legel_type_of_tp');
                if($tpType == 'government'){
                    $userController = new UserController();
                    $userController->log($rq->formForm,'user','initial_submit', 'Application send to HCSSC');

                    if(!empty($rq->undertaking_form)){
                        $file = $rq->file('undertaking_form');
                        $fileName = $user->username ."/undertaking_form-".date('ymdtms').".".$file->extension();
                        $file->storeAs('public/tpusers/', $fileName);
                        TPUser::where('id',$applicationId)->update(['undertaking_form_file'=> 'tpusers/{$fileName}']);
                    }
                }else{
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
                                $file->storeAs('public/tpusers/', $fileName);
                                $updatePaymentItems['payment_file']= "tpusers/{$fileName}";
                            }
                            if(TpPayments::where('tp_id',$rq->formForm)->where('payment_type',$rq->type)->update($updatePaymentItems)){
                                if(!empty($rq->undertaking_form)){
                                    $file = $rq->file('undertaking_form');
                                    $fileName = $user->username . "/undertaking_form-".date('ymdtms').".".$file->extension();
                                    $file->storeAs('public/tpusers/', $fileName);
                                    TPUser::where('id',$applicationId )->update(['undertaking_form_file'=>  "tpusers/{$fileName}" ]);
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
                                $file->storeAs('public/tpusers/', $fileName);
                                $payment->payment_file = "tpusers/{$fileName}";
                            }
                            if($payment->save()){
                                if(!empty($rq->undertaking_form)){
                                    $file = $rq->file('undertaking_form');
                                    $fileName = $user->username ."/undertaking_form-".date('ymdtms').".".$file->extension();
                                    $file->storeAs('public/tpusers/', $fileName);
                                    TPUser::where('id',$applicationId )->update(['undertaking_form_file'=> "tpusers/{$fileName}" ]);
                                }
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

        if ($formStage === 'tcIndusCon') {
            $industryNames = $rq->input('industry_name', []);

            if (!empty($industryNames) && count($industryNames) > 0) {
                foreach ($industryNames as $index => $industryName) {
                    if (empty($industryName)) { continue;    }

                    $recordId = $rq->input("indCon.$index");
                    $tcIndustryCon = $recordId ? TpIndustryConnection::where('id', $recordId)->where('tp_id', $applicationId)
                            ->firstOrNew() : new TpIndustryConnection();

                    $tcIndustryCon->tp_id = $applicationId;
                    foreach ($this->tcIndusConFields as $field) {
                        if ($rq->has("{$field}.{$index}")) {
                            $tcIndustryCon->$field = $rq->input("{$field}.{$index}");
                        }
                    }

                    foreach ($this->tcIndusConFiles as $field) {
                        $files = $rq->file($field);
                        if (!empty($files[$index])) {
                            $file = $files[$index];
                            if (!empty($tcIndustryCon->$field) && Storage::exists('public/' . $tcIndustryCon->$field)) {
                                Storage::delete('public/' . $tcIndustryCon->$field);
                            }
                            $fileName = $user->username . "/{$field}-" . now()->format('YmdHis') . "." . $file->extension();
                            $file->storeAs('public/tpusers/', $fileName);
                            $tcIndustryCon->$field = "tpusers/{$fileName}";
                        }
                    }
                    if (!$tcIndustryCon->save()) {
                        return response()->json(['error' => 'Unable to save industry connection data'], 500);
                    }
                }
            }
            $allGood = true;
        }

        if($formStage == 'tcFacility'){
            $tp = TPUser::find($applicationId);
            if (!$tp) {
                return response()->json(['error' => 'TC record not found, Please check again!',], 404);
            }

            DB::beginTransaction();
            try{
                $tcFacilities = TcFacilities::firstOrNew(['tp_id' => $applicationId]);
                foreach ($this->expTCFacilityFields as $key) {
                     if ($key === 'internet_speed_file' && $rq->hasFile($key)) {
                        $file = $rq->file($key);
                        if (!empty($tcFacilities->$key) && Storage::exists('public/' . $tcFacilities->$key)) {
                            Storage::delete('public/' . $tcFacilities->$key);
                        }
                        $fileName = $user->username . "/{$key}-" . now()->format('YmdHis') . "." . $file->extension();
                        $file->storeAs('public/tpusers/', $fileName);
                        $tcFacilities->$key = "tpusers/{$fileName}";

                    } elseif ($rq->has($key)) {
                        $tcFacilities->$key = $rq->input($key);
                    }
                }
                foreach ($this->expTCFacilityCheckBoxFields as $key) {
                    $tcFacilities->$key = $rq->has($key) ? 'Y' : 'N';
                }
                $tcFacilities->tp_id = $applicationId;
                $tcFacilities->save();


                $tcGalleries = TcGalleries::firstOrNew(['tp_id' => $applicationId]);
                foreach ($this->tcFacilityFiles as $key) {
                    if ($rq->hasFile($key)) {
                        $file = $rq->file($key);

                        if (!empty($tcGalleries->$key) && Storage::exists('public/' . $tcGalleries->$key)) {
                            Storage::delete('public/' . $tcGalleries->$key);
                        }
                        $fileName = $user->username . "/{$key}-" . now()->format('YmdHis') . "." . $file->extension();
                        $file->storeAs('public/tpusers/', $fileName);
                        $tcGalleries->$key = "tpusers/{$fileName}";
                    }
                }
                $tcGalleries->tp_id = $applicationId;
                $tcGalleries->save();

                DB::commit();
                $allGood = true;

            }catch (Exception $e) {
                DB::rollBack();
                Log::error("TC Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                dd($e);
                return back()->with('error', 'Unable to save Staff Details. Please try again.');
            }

        }

        if($formStage == 'tcQualification'){

            if (TcTrainers::where('tp_id',$applicationId)->exists()) {
                TcTrainers::where('tp_id', $applicationId)->delete();
            }
            if(isset($_REQUEST['eqQalTraner'])){
                foreach ($_REQUEST['eqQalTraner'] as $qulCode => $trainerValue) {
                    $tc_trainer = new TcTrainers();
                    $tc_trainer->tp_id = $applicationId;
                    foreach ($trainerValue as $trainKey => $trainValue) {
                        $tc_trainer->$trainKey = $trainValue;
                    }
                    if(!$tc_trainer->save()){die("unable to save data");}
                }
            }

            if (TpTrainerAndEquipment::where('tp_id',$applicationId)->exists()) {
                TpTrainerAndEquipment::where('tp_id', $applicationId)->delete();
            }
            if(!empty($_REQUEST['eqpData'])){
                foreach ($_REQUEST['eqpData'] as $qulCode => $toolValue) {
                    foreach ($toolValue as $toolName => $QAvail) {
                        $tc_trainer_and_equ = new TpTrainerAndEquipment();
                        $tc_trainer_and_equ->tp_id = $rq->formForm;
                        $tc_trainer_and_equ->short_qul_code = $qulCode;
                        $tc_trainer_and_equ->short_tool_name = $toolName;
                        $tc_trainer_and_equ->qty_avl = empty($QAvail)?"":$QAvail;
                        if(!empty($_REQUEST['rqpRemarks'][$qulCode][$toolName])){
                            $tc_trainer_and_equ->remark = $_REQUEST['rqpRemarks'][$qulCode][$toolName];
                        }
                        if(!$tc_trainer_and_equ->save()){die("unable to save data");}
                    }
                }
            }
            $allGood = true;
        }

        if ($formStage === 'tcRoomArea') {
            try {
                DB::beginTransaction();

                // TC data
                $tcenterData = [];
                foreach ($this->expTPInfraFields as $key) {
                    if ($rq->has($key)) {
                        $val = $rq->input($key);
                        $tcenterData[$key] = $val;
                    }
                }
                $tcenter = TCenter::updateOrCreate( ['tp_id' => $applicationId],  $tcenterData );

                // Laboratries data
                $tcInfraTypes = ['class', 'lab', 'hybrid'];
                foreach ($tcInfraTypes as $infraType) {

                    $roomNames = $rq->input("{$infraType}_room_name", []);
                    foreach ($roomNames as $index => $roomName) {
                        if (empty($roomName)) { continue; }
                        $infraId = $rq->input("{$infraType}_Infra.$index");

                        $tcInfra = new TpTrainingInfrastructure();
                        if (!empty($infraId)) {
                            $exists = DB::table('tp_training_infrastructures')->where('id', $infraId)->where('tp_id', $applicationId)->exists();
                            if ($exists) {
                                $tcInfra = TpTrainingInfrastructure::find($infraId);
                            }
                        }

                        $tcInfra->tp_id = $applicationId;
                        $tcInfra->type = $infraType;
                        $tcInfra->room_name = $roomName;
                        $tcInfra->carpet_area = $rq->input("{$infraType}_carpet_area.$index");
                        $tcInfra->furnished = $rq->input("{$infraType}_furnished.$index");
                        $tcInfra->infr_remark = $rq->input("{$infraType}_infr_remark.$index");

                        foreach ($this->tpQualification as $key) {
                            $val = $rq->input("{$infraType}_{$key}.$index");
                            if (!is_null($val)) {
                                $tcInfra->$key = $val;
                            }
                        }

                        if (!empty($rq->input("{$infraType}_qual_name.$index"))) {
                            $tcInfra->qual_code = MasterQualifications::where('id', $rq->input("{$infraType}_qual_name.$index"))->value('mq_code');
                        }
                        $tcInfra->save();
                    }
                }

                DB::commit();
                $allGood = true;

                // dd($rq  );

            } catch (Exception $e) {
                DB::rollBack();
                Log::error('TC Room Area Save Failed', [ 'error' => $e->getMessage(), 'stage' => $formStage, 'request' => $rq->all(),]);
                return back()->with('error', 'Unable to save training centre details. Please try again.');
            }
        }

        if($formStage == 'tcStaffDetails'){

            DB::beginTransaction();
            try {
                $username = $user->username;
                $names = $rq->input("staff_name", []);
                foreach ($names as $index => $name) {
                    if (empty($name)) continue;

                    $id = $rq->input("staff_id.{$index}");
                    $data = [
                        'tp_id' => $applicationId,
                        'staff_type' => "TC",
                        'name' => $name,
                        'phone' => $rq->input("staff_phone.{$index}"),
                        'email' => $rq->input("staff_email.{$index}"),
                        'education' => $rq->input("staff_education.{$index}"),
                        'designation' => $rq->input("staff_designation.{$index}"),
                        'experience' => $rq->input("staff_experience.{$index}"),
                        'remark' => $rq->input("staff_remark.{$index}"),
                    ];

                    if (!empty($id)) {
                        $staff = TpStaff::find($id);
                        if ($staff) {
                            foreach ($data as $key => $val) {
                                $staff->$key = $val;
                            }
                            $staff->save();
                            continue;
                        }
                    }

                    $staff = new TpStaff();
                    foreach ($data as $key => $val) {
                        $staff->$key = $val;
                    }
                    $staff->save();
                }

                DB::commit();
                $allGood = true;

            } catch (Exception $e) {
                DB::rollBack();
                Log::error("TC Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                dd($e);
                return back()->with('error', 'Unable to save Staff Details. Please try again.');
            }
        }

        if ($formStage === 'tcGenDetails') {
            sleep(5);

            try{
                $inputData = [];
                foreach ($this->tcGenDetails as $field) {
                    if ($rq->has($field) && !empty($rq->$field)) {
                        $inputData[$field] = $rq->$field;
                    }
                }
                $inputData['tp_id'] = $applicationId;

                // Handle file uploads
                foreach ($this->expFilesTCGenDetails as $field) {
                    if ($rq->hasFile($field)) {
                        $file = $rq->file($field);
                        $fileName = $user->username . "/" . $field . "-" . now()->format('ymdHis') . "." . $file->extension();
                        $file->storeAs('public/tpusers', $fileName);
                        $inputData[$field] = "tpusers/{$fileName}";
                    }
                }
                $tcenter = TCenter::where('tp_id', $applicationId)->first();
                if(!$tcenter){
                    $tcenter = new TCenter();
                }
                $tcenter->fill($inputData);

                if (!$tcenter->save()) {
                    abort(500, "Unable to save Training Center data");
                }
                $allGood = true;
            }catch (\Exception $e) {
                DB::rollBack();
                Log::error("TP Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                return back()->with('error', 'Unable to save Staff Details. Please try again.');
            }
        }

        if ($formStage == 'tpMngStaffDetails') {
            DB::beginTransaction();
            try {
                $username = $user->username;
                $categories = [ 'dir' => 'Owner',  'spoc' => 'SPOC', 'staff' => 'Management' ];

                foreach ($categories as $prefix => $type) {
                    $names = $rq->input("{$prefix}_name", []);
                    foreach ($names as $index => $name) {
                        if (empty($name)) continue;

                        $id = $rq->input("{$prefix}_id.{$index}");
                        $data = [
                            'tp_id' => $applicationId,
                            'staff_type' => $type,
                            'name' => $name,
                            'phone' => $rq->input("{$prefix}_phone.{$index}"),
                            'email' => $rq->input("{$prefix}_email.{$index}"),
                            'alt_phone' => $rq->input("{$prefix}_alt_phone.{$index}"),
                            'education' => $rq->input("{$prefix}_education.{$index}"),
                            'designation' => $rq->input("{$prefix}_designation.{$index}"),
                            'experience' => $rq->input("{$prefix}_experience.{$index}"),
                            'remark' => $rq->input("{$prefix}_remark.{$index}"),
                        ];

                        // Handle resume file upload
                        $files = $rq->file("{$prefix}_resume");
                        if (!empty($files[$index])) {
                            $file = $files[$index];
                            $fileName = "{$prefix}_resume-{$index}-" . now()->format('YmdHis') . "." . $file->extension();
                            $file->storeAs("public/tpusers/{$username}", $fileName);
                            $data['resume'] = "tpusers/{$username}/{$fileName}";
                        }

                        if (!empty($id)) {
                            $staff = TpStaff::find($id);
                            if ($staff) {
                                foreach ($data as $key => $val) {
                                    $staff->$key = $val;
                                }
                                $staff->save();
                                continue;
                            }
                        }

                        $staff = new TpStaff();
                        foreach ($data as $key => $val) {
                            $staff->$key = $val;
                        }
                        $staff->save();

                    }
                }

                DB::commit();
                $allGood = true;

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("TP Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                return back()->with('error', 'Unable to save Staff Details. Please try again.');
            }
        }

        if ($formStage == 'tpMngDetails') {

            DB::beginTransaction();
            try {
                $username = $user->username;
                $tpuser = TPUser::find($applicationId);

                $tpuser->exists = $tpuser->exists || false;
                $tpuser->exists = true;
                $tpuser->id = $applicationId;

                foreach ($rq->only($this->tpMngDetails) as $key => $value) {
                    $tpuser->$key = $value;
                }
                foreach ($this->expFilesTPManagment as $fieldName) {
                    if ($rq->hasFile($fieldName)) {
                        $file = $rq->file($fieldName);
                        $fileName = "{$fieldName}-" . now()->format('YmdHis') . "." . $file->extension();
                        $file->storeAs("public/tpusers/{$username}", $fileName);
                        $tpuser->$fieldName = "tpusers/{$username}/{$fileName}";
                    }
                }

                $tpuser->save();


                // --- Turnover Details ---
                $financialYears = $rq->input('financial_yrs', []);
                $savedIds = [];

                foreach ($financialYears as $index => $financialYear) {
                    if (empty($financialYear)) continue;

                    $turnOverId = $rq->input("turnOver.{$index}");
                    $turnoverData = ['tp_id' => $applicationId];
                    foreach ($this->expTCAuditReportField as $key) {
                        $turnoverData[$key] = $rq->input("{$key}.{$index}");
                    }

                    foreach ($this->expTCAuditReportFile as $inputField) {
                        $files = $rq->file($inputField);
                        if (!empty($files[$index])) {
                            $file = $files[$index];
                            $fileName = "{$inputField}-{$index}-" . now()->format('YmdHis') . "." . $file->extension();
                            $file->storeAs("public/tpusers/{$username}", "{$fileName}");
                            $turnoverData[$inputField] = "tpusers/{$username}/{$fileName}";
                        }
                    }

                    if (!empty($turnOverId)) {
                        $turnover = TpAuditTurnover::where('id', $turnOverId)->where('tp_id', $applicationId)
                            ->first();

                        if ($turnover) {
                            $turnover->update($turnoverData);
                        } else {
                            $turnover = TpAuditTurnover::create($turnoverData);
                        }
                    } else {
                        $turnover = TpAuditTurnover::create($turnoverData);
                    }
                    $savedIds[] = $turnover->id;
                }

                if (!empty($savedIds)) {
                    TpAuditTurnover::where('tp_id', $applicationId)->whereNotIn('id', $savedIds)->delete();
                }

                DB::commit();
                $allGood = true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("TP Management Save Error: " . $e->getMessage() . " Line No - " . $e->getLine());
                return back()->with('error', 'Unable to save TP Management Details. Please try again.');
            }
        }

        if($formStage == 'tpGenDetails'){
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

            foreach ($this->expFilesTPGenDetails as $inputField) {
                if ($rq->hasFile($inputField)) {
                    $file = $rq->file($inputField);
                    $fileName = $inputField . '-' . now()->format('ymdHis') . '.' . $file->extension();
                    $file->storeAs('tpusers/' . $user->username, $fileName, 'public');
                    $tpuser->$inputField = 'tpusers/' . $user->username . '/' . $fileName;
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
                    return redirect("/tp/application/".  $applicationId  ."/".$formStage);
                }else if($rq->formAction == 'finalSubmit'){
                    $tpuser = new TPUser();
                    $tpuser->exists = true;
                    $tpuser->id = $applicationId ;
                    $tpuser->tp_form_progress = "completed";
                    $tpuser->pre_submit_status = 'submited';
                    $tpuser->status = 'Pending';
                    $tpuser->post_submit_status = 'payment_pending';
                    $tpuser->save();
                    return redirect("/tp/application/". $applicationId ."/".$rq->formAction);
                }else{

                    $formSteps = [ 'tpGenDetails','tpMngDetails','tpMngStaffDetails','tcGenDetails','tcStaffDetails','tcRoomArea','tcQualification', 'tcFacility','tcIndusCon','formPaymentDetails','finalSubmit'];
                    $savedStep   = $tpuser->tp_form_progress ?? 'tpGenDetails';
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
                    return redirect("/tp/application/". $applicationId ."/".$rq->formAction);

                }
            }else{
                return redirect("/tp/newApplication");
            }
        }else{
            die("Error Saving the data");
        }
    }



    public function deleteEntry(Request $rq)
    {
        $user = auth()->user();
        if($rq->target == 'TPTurnOver'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId  >  0){
                return DB::table('tp_audit_turnovers')->where('id', $rq->targetId)->where('tp_id',$rq->tpId)->delete();
            }
        }
        if($rq->target == 'TPstaff' || $rq->target == "tcStaff"){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('tp_staff')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }
        if($rq->target == 'infraRMV'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('tp_training_infrastructures')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }

        if($rq->target == 'inConRMV'){

            if($user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId > 0){
                $tpConnection = TpIndustryConnection::find($rq->targetId);
                if (!$tpConnection) {
                    return response()->json(['error' => 'Industry connection not found'], 404);
                }

                $tpuser = TPUser::where('user_id', $user->id)->where('id', $tpConnection->tp_id)->value('id');
                if ($tpConnection->tp_id  ==  $tpuser ){
                    foreach ($this->tcIndusConFiles as $fileField) {
                        if (!empty($tpConnection->$fileField) && Storage::exists('public/' . $tpConnection->$fileField)) {
                            Storage::delete('public/' . $tpConnection->$fileField);
                        }
                    }
                    return $tpConnection->delete();
                }
            }
        }

    }

    public function checkFormProgress($applicationId, $formStage)
    {
        $tpUser = TPUser::find($applicationId);
        if (!$tpUser) {
            return redirect('tp/application/new/tpGenDetails')
                ->with('error', 'Please start from the beginning.');
        }

        $formSteps = [
            'tpGenDetails',
            'tpMngDetails',
            'tpMngStaffDetails',
            'tcGenDetails',
            'tcStaffDetails',
            'tcRoomArea',
            'tcQualification',
            'tcFacility',
            'tcIndusCon',
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
            return redirect("/tp/application/{$applicationId}/{$redirectStep}")
                ->with('error', 'Please complete previous steps before continuing.');
        }

        // If same or previous step — allow access
        return true;
    }


}
