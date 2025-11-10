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
        Schema::create('t_centers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->string('tc_name')->nullable();
            $table->string('tc_type')->nullable();
            $table->string('affiliation_name')->nullable();
            $table->date('validity_start_date')->nullable();
            $table->date('validity_end_date')->nullable();
            $table->text('affiliation_details')->nullable();
            $table->text('other_remarks')->nullable();

            // Training Center Address
            $table->text('address')->nullable();
            $table->string('nearby_landmark')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('plus_code_address')->nullable();
            $table->string('address_proof_document')->nullable();


            $table->text('lang_of_instruction')->nullable();
            $table->text('lang_of_instruction_other')->nullable();
            $table->text('total_net_carpet_area')->nullable();
            $table->text('add_covered_area')->nullable();



            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_centers');
    }
};
