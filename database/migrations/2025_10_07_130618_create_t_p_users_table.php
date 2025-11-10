<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('t_p_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('application_no');
            $table->string('tp_name');
            $table->string('type_of_tp');
            $table->string('tproof_file')->nullable();
            $table->text('remark')->nullable();
            $table->text('mission_objective')->nullable();

            // Postal Address
            $table->text('address')->nullable();
            $table->string('nearby_landmark')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('plus_code_address')->nullable();
            $table->string('address_proof_type')->nullable();
            $table->string('address_proof_file')->nullable(); // file path

            // Contact Details
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website_link')->nullable();
            $table->integer('additional_branches_count')->nullable();

            // VTP Premises Photos
            $table->string('outside_front_view')->nullable();
            $table->string('outside_right_view')->nullable();
            $table->string('outside_other_image')->nullable();
            $table->string('inside_entrance')->nullable();
            $table->string('inside_other_image')->nullable();

            $table->string('yoe')->nullable();
            $table->string('yoe_proof')->nullable();
            $table->string('pan')->nullable();
            $table->string('pan_proof')->nullable();
            $table->string('gst')->nullable();
            $table->string('gst_proof')->nullable();

            $table->string('account_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_proof')->nullable();

            $table->string('pre_submit_status')->nullable();
            $table->string('post_submit_statu')->nullable();
            $table->string('finalPaymentRemark')->nullable();
            $table->string('initial_payment_remark')->nullable();
            $table->string('reject_remark')->nullable();
            $table->string('correction_remarks')->nullable();

            $table->string('tp_form_progress')->nullable();

            $table->string('status')->nullable();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('t_p_users');
    }
};
