<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Admin\EndUserCharges;
use App\Models\Admin\MasterQualifications;
use App\Models\EndUser\CoeAwards;
use App\Models\EndUser\CoECenter;
use App\Models\EndUser\CoEFundingAgencies;
use App\Models\EndUser\CoeReserch;
use App\Models\EndUser\IndustryPartners;
use App\Models\EndUser\TpTrainerAndEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EndUser\TCenter;
use App\Models\EndUser\TcFacilities;
use App\Models\EndUser\TcGalleries;
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

class CoERegistrationController extends Controller
{


    public $tpGenDetails, $expFilesTPGenDetails, $tpMngDetails, $tpMngFiles, $inDirectors ,  $inBranches, $inBranchesFiles, $inAffidiateFiles;
    public $expTCAuditReportField, $expTCAuditReportFile;

    public $expTCFacilityFields, $expTCFacilityCheckBoxFields, $tcFacilityFiles, $expTPInfraFields, $tpQualification;

    public $coeStudentDetails, $coeStudentOthFields;

    public function __construct() {


        // Step 1
        $this->tpGenDetails = ['tp_name', 'legel_type_of_tp', 'legal_type_other', 'registration_no', 'yoe', 'legal_remark', 'address','pin_code','state','district','city','plus_code_address',
                                'address_proof_type', 'telephone_number', 'fax_no', 'contact_number', 'email', 'website_link' ];
        $this->expFilesTPGenDetails = [ 'legel_type_roof_file',"yoe_proof", 'address_proof_file'];

        $this->expTCAuditReportField = ["financial_yrs","turn_over","remark"];
        $this->expTCAuditReportFile = ["report_file"];

        // Setp 2
        $this->tpMngDetails = [ 'account_no', 'bank_name','ifsc_code' ];
        $this->tpMngFiles = ["bank_proof"];

        // Step 3
        $this->expTPInfraFields = ["lang_of_instruction", "lang_of_instruction_other", "total_net_carpet_area", "add_covered_area"];
        $this->tpQualification = ["qual_name","qual_code","qual_sub_sector","qual_trainee_to_trainer_ratio","qual_associated_classroom","qual_associated_lab","qual_no_of_parallel_batch","qual_trainer_ava"];

        // Step 4
        $this->expTCFacilityFields = ["bldg_lvls", "const_bldg_struct", "prox_pub_trans", "nearest_station", "approach_road", "internet_speed", "internet_speed_file", "difabled_details", "facility_remarks"];
        $this->expTCFacilityCheckBoxFields = ["security_guards", "biometric_attend", "greenery_surround", "power_backup", "training_centre", "cctv_cam_rec", "drinking_water", "housekeeping_staff", "clean_washrooms",
         "fire_extinguisher", "fire_hose_pipe", "first_aid_kit", "fire_safety_instr", "emergency_numbers", "med_safety_facil", "pantry", "library", "parking", "staff_room", "storehouse"];
        $this->tcFacilityFiles = ["image_outarea", "image_approach_road", "image_tc", "image_tc_front", "image_tc_back", "image_tc_left", "image_tc_right", "image_net_bill", "image_biometric_device",
        "image_classroom", "image_lab", "image_firstaid", "image_fire", "image_water", "image_insecption_card", "image_washroom", "image_reception", "image_placementcell", "image_counselling", "image_library",
         "image_office", "image_pantry", "image_parking"];


        // Step 7 Students
        $this->coeStudentDetails = ["staff_type", "name", "phone", "email", "education",  "experience", "resume", "remark"];
        $this->coeStudentOthFields = ['coe_incubation_details', 'coe_technology_usege'];


        $this->inDirectors  = ["staff_type", "name", "phone", "email", "father_name", "address", "remark"];
        $this->inBranches = ['office_name','address','pin_code','state','district','city','plus_code_address','address_proof_type'];
        $this->inBranchesFiles = ['address_proof_file'];

        // Step 4
         $this->inAffidiateFiles  = ["undertaking_form_file", "coe_dpr_file", "coe_ca_certificate", "digital_signature"];

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

            return view("enduser/coe/applications/in-payment-details",[
                'formStage'=>$formStage,
                'formId'=> $formId,

                'tpUser'=> $tpUser,
                'tcType'=> $tpUser->legel_type_of_tp,
                'payments'=>$payment,
                'endUserPayments'=>$endUserPayments,
            ]);


        }

        if($formStage == 'coefinalDocuments'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $coeData = $this->inAffidiateFiles;

            $tpuser = TPUser::find($formId)->toArray();
            foreach ($coeData as $key) {
                if(array_key_exists($key, $tpuser)){
                    $coeData[$key] = $tpuser[$key];
                }else{
                    $coeData[$key] = "";
                }
            }

            return view("enduser/coe/applications/coe-documents",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'formStageData'=> $coeData,
            ]);
        }

        if($formStage == 'coeStudentSupport'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $formData = [];
            $coeData = TPUser::find($applicationId);
            if (!$coeData) {
                abort(404, "TP data not found");
            }
            foreach ($this->coeStudentOthFields as $key) { $formData[$key] = $coeData->$key ?? '';  }

            $coeStudents = TpStaff::where('tp_id', $formId)->where('staff_type','Students')->get()->toArray();

            return view("enduser/coe/applications/coe-students", [
                'formStage'         => $formStage,
                'formId'            => $formId,
                'coeStudents'       => $coeStudents,
                'formData'          => $formData,
            ]);
        }


        if($formStage == 'coeReserch'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $coeAwards = CoeAwards::where('tp_id', $formId)->get()->toArray();
            $coeResearch = CoeReserch::where('tp_id', $formId)->get()->toArray();

            return view("enduser/coe/applications/coe-reserch-form", [
                'formStage'          => $formStage,
                'formId'             => $formId,
                'coeAwards'          =>$coeAwards,
                'coeResearch'        =>$coeResearch,
            ]);
        }

        if($formStage == 'coePartners'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;
            $coeAgencies = CoEFundingAgencies::where('tp_id', $formId)->get()->toArray();
            $coePartners = IndustryPartners::where('tp_id', $formId)->get()->toArray();

            return view("enduser/coe/applications/coe-partner-deed", [
                'formStage'          => $formStage,
                'formId'             => $formId,
                'coePartners'        =>$coePartners,
                'coeAgencies'        =>$coeAgencies,
            ]);
        }


        if($formStage == 'coeFacility'){
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

            return view("enduser/coe/applications/coe-facilities",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'formStageData'=> $sendBackArray,
            ]);
        }


        if($formStage == 'coeRoomArea'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $qualiAll = MasterQualifications::where('status', 'active')->get()->toArray();

            $tcInfra = DB::Select('select '.implode(',',$this->expTPInfraFields).' from co_e_centers WHERE 1  AND co_e_centers.tp_id = '.$applicationId);

            $tcClases = TpTrainingInfrastructure::where('tp_id', $formId)->where('type','class')->get()->toArray();
            $tclabs = TpTrainingInfrastructure::where('tp_id', $formId)->where('type','lab')->get()->toArray();
            $tcHybridlabs = TpTrainingInfrastructure::where('tp_id', $formId)->where('type','hybrid')->get()->toArray();

            return view("enduser/coe/applications/coe-room-area",[
                'formStage'=>$formStage,
                'formId'=> $formId,
                'masterQualifications'=> $qualiAll,
                'tcInfraData' => !empty($tcInfra) ? json_decode(json_encode($tcInfra[0]), true) : [],
                'tcClases'=> $tcClases,
                'tclabs'=> $tclabs,
                'thybridlabs'=> $tcHybridlabs,
            ]);
        }

        if($formStage == 'coeStaffDetails'){
            $redirect = $this->checkFormProgress($applicationId, $formStage);
            if ($redirect !== true) return $redirect;

            $tpStaffDir = TpStaff::where('tp_id', $formId)->where('staff_type','Owner')->get()->toArray();
            $tpStaffSPOC = TpStaff::where('tp_id', $formId)->where('staff_type','SPOC')->get()->toArray();
            $tpStaffgen = TpStaff::where('tp_id', $formId)->where('staff_type','Management')->get()->toArray();

            return view("enduser/coe/applications/coe-staff-details", [
                'formStage'        => $formStage,
                'formId'           => $formId,
                'tpDirStaffs'       =>$tpStaffDir,
                'tpSPOCs'           =>$tpStaffSPOC,
                'tpStaffGens'       =>$tpStaffgen
            ]);
        }

        if($formStage == 'coeGenDetails'){

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

            // Handle turnover reports
            $turnoverFields = array_merge( $this->expTCAuditReportField, $this->expTCAuditReportFile);
            $tpTurnOverReports = TpAuditTurnover::where('tp_id', $applicationId)->get();
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

            return view("enduser/coe/applications/coe-gen-details",[
                'formStage'        =>$formStage,
                'formId'           => $formId,
                'formStageData'    => $formData,
                'allTurnOver'      => $multipleSendBackArray['tp_turn_over_report'],
                'TpAffTcAccGuidelines'=>session('TpAffTcAccGuidelines'),
            ]);
        }

    }



    public function coeFormSubmitSteps($formStage, Request $rq)
    {
        $allGood = false;
        $user = auth()->user();
        $applicationId = $rq->formForm;
        $username = $user->username;

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
                            $file->storeAs('public/coeusers/', $fileName);
                            $updatePaymentItems['payment_file']= "coeusers/{$fileName}";
                        }
                        if(TpPayments::where('tp_id',$rq->formForm)->where('payment_type',$rq->type)->update($updatePaymentItems)){
                            if(!empty($rq->undertaking_form)){
                                $file = $rq->file('undertaking_form');
                                $fileName = $user->username . "/undertaking_form-".date('ymdtms').".".$file->extension();
                                $file->storeAs('public/coeusers/', $fileName);
                                TPUser::where('id',$applicationId )->update(['undertaking_form_file'=>  "coeusers/{$fileName}" ]);
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
                            $file->storeAs('public/coeusers/', $fileName);
                            $payment->payment_file = "coeusers/{$fileName}";
                        }
                        if($payment->save()){
                            if(!empty($rq->undertaking_form)){
                                $file = $rq->file('undertaking_form');
                                $fileName = $user->username ."/undertaking_form-".date('ymdtms').".".$file->extension();
                                $file->storeAs('public/coeusers/', $fileName);
                                TPUser::where('id',$applicationId )->update(['undertaking_form_file'=> "coeusers/{$fileName}" ]);
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

        if($formStage == 'coefinalDocuments'){
            $tp = TPUser::find($applicationId);
            if (!$tp) {
                return response()->json(['error' => 'CoE record not found, Please check again!',], 404);
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

                        $fileName = "coeusers/" . $user->username . "/{$field}-" . now()->format('YmdHis') . "." . $file->extension();
                        $file->storeAs('public', $fileName);
                        $tp->$field = $fileName;
                    }
                }

                $tp->save();

                DB::commit();
                $allGood = true;
            }catch (Exception $e) {
                DB::rollBack();
                Log::error("COE Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                return back()->with('error', 'Unable to save documents Details. Please try again.');
            }

        }

        if($formStage == 'coeStudentSupport'){

            $coeUser = TPUser::find($applicationId);
            if (!$coeUser) {
                return response()->json(['error' => 'CoE record not found, Please check again!',], 404);
            }

            DB::beginTransaction();
            try{

                $coedatas =  $this->coeStudentOthFields;
                foreach ($coedatas as $field) {
                    if ($rq->has($field)) {
                        $coeUser->$field = $rq->input($field);
                    }
                }
                $coeUser->save();

                $names = $rq->input("student_name", []);
                foreach ($names as $index => $name) {
                    if (empty($name)) continue;
                    $id = $rq->input("student_id.{$index}");
                    $data = [
                        'tp_id' => $applicationId,
                        'staff_type' => 'Students',
                        'name' => $name,
                        'phone' => $rq->input("student_phone.{$index}"),
                        'email' => $rq->input("student_email.{$index}"),
                        'education' => $rq->input("student_education.{$index}"),
                        'experience' => $rq->input("student_experience.{$index}"),
                        'remark' => $rq->input("student_remark.{$index}"),
                    ];

                    // Handle resume file upload
                    $files = $rq->file("student_resume");
                    if (!empty($files[$index])) {
                        $file = $files[$index];
                        if (!empty($id)) {
                            $existingStaff = TpStaff::find($id);
                            if ($existingStaff && !empty($existingStaff->resume) && Storage::exists('public/' . $existingStaff->resume)) {
                                Storage::delete('public/' . $existingStaff->resume);
                            }
                        }
                        $fileName = "student_resume-{$index}-" . now()->format('YmdHis') . "." . $file->extension();
                        $file->storeAs("public/coeusers/{$username}", $fileName);
                        $data['resume'] = "coeusers/{$username}/{$fileName}";
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

                DB::commit();
                $allGood = true;
            }catch (Exception $e) {
                DB::rollBack();
                Log::error("COE Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                return back()->with('error', 'Unable to save documents Details. Please try again.');
            }

        }

        if ($formStage == 'coeReserch') {

            DB::beginTransaction();
            try {

                // Awards
                $awardNames = $rq->input('award_category', []);
                foreach ($awardNames as $index => $name) {
                    if (empty($name)) continue;

                    $id = $rq->input("award_id.{$index}");
                    $data = [
                        'tp_id'          => $applicationId,
                        'category'       => $name,
                        'area'           => $rq->input("award_area.{$index}"),
                        'year'           => $rq->input("award_date.{$index}"),
                        'remark'         => $rq->input("award_remark.{$index}"),
                    ];

                    $files = $rq->file("award_file");
                    if (!empty($files[$index])) {
                        $file = $files[$index];

                        if (!empty($id)) {
                            $existingAward = CoeAwards::find($id);
                            if ($existingAward && !empty($existingAward->award_file) && Storage::exists('public/' . $existingAward->award_file)) {
                                Storage::delete('public/' . $existingAward->award_file);
                            }
                        }

                        $fileName = "award_file-{$index}-" . now()->format('YmdHis') . "." . $file->extension();
                        $file->storeAs("public/coeusers/{$username}", $fileName);
                        $data['award_file'] = "coeusers/{$username}/{$fileName}";
                    }

                    if (!empty($id)) {
                        $award = CoeAwards::find($id);
                        if ($award) {
                            foreach ($data as $key => $value) {
                                $award->$key = $value;
                            }
                            $award->save();
                            continue;
                        }
                    }

                    $coeAwards = new CoeAwards();
                    foreach ($data as $key => $value) {
                        $coeAwards->$key = $value;
                    }
                    $coeAwards->save();
                }

                // Research
                $researchNames = $rq->input('research_subject', []);
                foreach ($researchNames as $index => $name) {
                    if (empty($name)) continue;

                    $id = $rq->input("research_id.{$index}");
                    $data = [
                        'tp_id'          => $applicationId,
                        'subject'        => $name,
                        'year'           => $rq->input("research_year.{$index}"),
                        'description'    => $rq->input("research_description.{$index}"),
                    ];
                    if (!empty($id)) {
                        $research = CoeReserch::find($id);
                        if ($research) {
                            foreach ($data as $key => $value) {
                                $research->$key = $value;
                            }
                            $research->save();
                            continue;
                        }
                    }

                    $research = new CoeReserch();
                    foreach ($data as $key => $value) {
                        $research->$key = $value;
                    }
                    $research->save();
                }

                DB::commit();
                $allGood = true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("TP Staff Save Error: " . $e->getMessage() . " Line: " . $e->getLine());
                dd($e);
                return back()->with('error', 'Unable to save Staff Details. Please try again.');
            }
        }

        if ($formStage == 'coePartners') {

            DB::beginTransaction();
            try {
                $username = $user->username;

                // partners
                $agencyNames = $rq->input('agency_name', []);
                foreach ($agencyNames as $index => $name) {
                    if (empty($name)) continue;

                    $id = $rq->input("agency_id.{$index}");
                    $data = [
                        'tp_id'          => $applicationId,
                        'agency_name'     => $name,
                        'year'  => $rq->input("year.{$index}"),
                        'amount'        => $rq->input("amount.{$index}"),
                        'remark'         => $rq->input("remark.{$index}"),
                    ];

                    if (!empty($id)) {
                        $agency = CoEFundingAgencies::find($id);
                        if ($agency) {
                            foreach ($data as $key => $value) {
                                $agency->$key = $value;
                            }
                            $agency->save();
                            continue;
                        }
                    }

                    $agency = new CoEFundingAgencies();
                    foreach ($data as $key => $value) {
                        $agency->$key = $value;
                    }
                    $agency->save();
                }

                // Agencies
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

        if ($formStage === 'coeRoomArea') {

            $tp = TPUser::find($applicationId);
            if (!$tp) {
                return response()->json(['error' => 'CoE record not found, Please check again!',], 404);
            }

            try {
                DB::beginTransaction();

                $coeData = [];
                foreach ($this->expTPInfraFields as $key) {
                    if ($rq->has($key)) {
                        $val = $rq->input($key);
                        $coeData[$key] = $val;
                    }
                }

                CoECenter::updateOrCreate( ['tp_id' => $applicationId],  $coeData );

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
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('TC Room Area Save Failed', [ 'error' => $e->getMessage(), 'stage' => $formStage, 'request' => $rq->all(),]);
                dd($e);
                return back()->with('error', 'Unable to save training centre details. Please try again.');
            }
        }

        if($formStage == 'coeFacility'){
            $tp = TPUser::find($applicationId);
            if (!$tp) {
                return response()->json(['error' => 'CoE record not found, Please check again!',], 404);
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
                        $file->storeAs('public/coeusers/', $fileName);
                        $tcFacilities->$key = "coeusers/{$fileName}";

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
                        $file->storeAs('public/coeusers/', $fileName);
                        $tcGalleries->$key = "coeusers/{$fileName}";
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

        if ($formStage == 'coeStaffDetails') {

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
                            'education' => $rq->input("{$prefix}_education.{$index}"),
                            'designation' => $rq->input("{$prefix}_designation.{$index}"),
                            'experience' => $rq->input("{$prefix}_experience.{$index}"),
                            'remark' => $rq->input("{$prefix}_remark.{$index}"),
                        ];

                        // Handle resume file upload
                        $files = $rq->file("{$prefix}_resume");
                        if (!empty($files[$index])) {
                            $file = $files[$index];

                            if (!empty($id)) {
                                $existingStaff = TpStaff::find($id);
                                if ($existingStaff && !empty($existingStaff->resume) && Storage::exists('public/' . $existingStaff->resume)) {
                                    Storage::delete('public/' . $existingStaff->resume);
                                }
                            }

                            $fileName = "{$prefix}_resume-{$index}-" . now()->format('YmdHis') . "." . $file->extension();
                            $file->storeAs("public/coeusers/{$username}", $fileName);
                            $data['resume'] = "coeusers/{$username}/{$fileName}";
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

        if($formStage == 'coeGenDetails'){
            $request_type = $rq->formForm;


            DB::beginTransaction();
            try{

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
                        $file->storeAs('coeusers/' . $user->username, $fileName, 'public');
                        $tpuser->$inputField = 'coeusers/' . $user->username . '/' . $fileName;
                    }
                }

                $tpuser->legel_type_of_tp = "Other";
                $tpuser->save();

                // --- Turnover Details ---

                $applicationId = $tpuser->id;
                $financialYears = $rq->input('financial_yrs', []);
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
                            $file->storeAs("public/coeusers/{$username}", "{$fileName}");
                            $turnoverData[$inputField] = "coeusers/{$username}/{$fileName}";
                        }
                    }

                    if (!empty($turnOverId)) {
                        $turnover = TpAuditTurnover::where('id', $turnOverId)->where('tp_id', $applicationId)->first();
                        if ($turnover) {
                            $turnover->update($turnoverData);
                        } else {
                            $turnover = TpAuditTurnover::create($turnoverData);
                        }
                    } else {
                        $turnover = TpAuditTurnover::create($turnoverData);
                    }
                }

                DB::commit();

                $allGood = true;
                $applicationId = $tpuser->id ;

            }catch(Exception $e){
                DB::rollBack();
                Log::error('TC Room Area Save Failed', [ 'error' => $e->getMessage(), 'stage' => $formStage, 'request' => $rq->all(),]);
                dd($e);
                return back()->with('error', 'Unable to save training centre details. Please try again.');
            }
        }

        if($allGood){
            if(!empty($rq->formAction)){
                if($rq->formAction == "saveOnly"){
                    return redirect("/coe/application/".  $applicationId  ."/".$formStage);
                }else if($rq->formAction == 'finalSubmit'){
                    $tpuser = new TPUser();
                    $tpuser->exists = true;
                    $tpuser->id = $applicationId ;
                    $tpuser->tp_form_progress = "completed";
                    $tpuser->pre_submit_status = 'submited';
                    $tpuser->status = 'Pending';
                    $tpuser->post_submit_status = 'payment_pending';
                    $tpuser->save();
                    return redirect("/coe/application/". $applicationId ."/".$rq->formAction);
                }else{

                    $formSteps = [ 'coeGenDetails','coeStaffDetails','coeRoomArea', 'coeFacility','coePartners', 'coeReserch', 'coeStudentSupport', 'coefinalDocuments', 'formPaymentDetails','finalSubmit'];
                    $savedStep   = $tpuser->tp_form_progress ?? 'coeGenDetails';
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
                    return redirect("/coe/application/". $applicationId ."/".$rq->formAction);

                }
            }else{
                return redirect("/coe/newApplication");
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
        if($rq->target == 'industryPartner'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('industry_partners')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }
        if($rq->target == 'coeAgency'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('co_e_funding_agencies')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }
        if($rq->target == 'coeAwards'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('coe_awards')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
            }
        }
        if($rq->target == 'coeReaserch'){
            if( $user && !empty($rq->tpId) && is_numeric($rq->targetId) && $rq->targetId>0){
                return DB::table('coe_reserches')->where('id', $rq->targetId)->where('tp_id',$rq->tpId )->delete();
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
            return redirect('coe/application/new/coeGenDetails')
                ->with('error', 'Please start from the beginning.');
        }

        $formSteps = [
            'coeGenDetails',
            'coeStaffDetails',
            'coeRoomArea',
            'coeFacility',
            'coePartners',
            'coeReserch',
            'coefinalDocuments',
            'coeStudentSupport',
            'formPaymentDetails',
            'finalSubmit',
            'completed'
        ];

        $savedStep = $tpUser->tp_form_progress ?? 'coeGenDetails';

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
            $redirectStep = $formSteps[$savedIndex] ?? 'coeGenDetails';
            return redirect("/coe/application/{$applicationId}/{$redirectStep}")
                ->with('error', 'Please complete previous steps before continuing.');
        }

        // If same or previous step — allow access
        return true;
    }


}
