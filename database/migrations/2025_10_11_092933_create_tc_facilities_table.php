<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('tc_facilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->text('bldg_lvls')->nullable();
            $table->text('const_bldg_struct')->nullable();
            $table->text('prox_pub_trans')->nullable();
            $table->text('nearest_station')->nullable();
            $table->text('approach_road')->nullable();
            $table->text('internet_speed')->nullable();
            $table->text('internet_speed_file')->nullable();
            $table->text('difabled_details')->nullable();
            $table->text('facility_remarks')->nullable();

            $table->string('security_guards')->nullable();
            $table->string('biometric_attend')->nullable();
            $table->string('greenery_surround')->nullable();
            $table->string('power_backup')->nullable();
            $table->string('training_centre')->nullable();
            $table->string('cctv_cam_rec')->nullable();
            $table->string('drinking_water')->nullable();
            $table->string('housekeeping_staff')->nullable();
            $table->string('clean_washrooms')->nullable();

            $table->string('fire_extinguisher')->nullable();
            $table->string('fire_hose_pipe')->nullable();
            $table->string('first_aid_kit')->nullable();
            $table->string('fire_safety_instr')->nullable();
            $table->string('emergency_numbers')->nullable();
            $table->string('med_safety_facil')->nullable();
            $table->string('pantry')->nullable();
            $table->string('library')->nullable();
            $table->string('parking')->nullable();
            $table->string('staff_room')->nullable();
            $table->string('storehouse')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_facilities');
    }
};
