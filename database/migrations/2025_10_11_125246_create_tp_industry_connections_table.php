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
        Schema::create('tp_industry_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->string('industry_name')->nullable();
            $table->text('industry_address')->nullable();
            $table->string('industry_spoc_name')->nullable();
            $table->string('industry_phone')->nullable();
            $table->string('industry_scale')->nullable();
            $table->string('industry_email')->nullable();
            $table->string('industry_placement')->nullable();
            $table->text('industry_remarks')->nullable();
            $table->string('industry_experts_eng_pro')->nullable();
            $table->text('industry_experts_eng_pro_file')->nullable();
            $table->string('curriculum_dev')->nullable();
            $table->text('curriculum_dev_file')->nullable();
            $table->string('job_support')->nullable();
            $table->text('job_support_file')->nullable();
            $table->string('guest_faculty')->nullable();
            $table->text('guest_faculty_file')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_industry_connections');
    }
};
