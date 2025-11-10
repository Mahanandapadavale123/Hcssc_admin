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
        Schema::create('tc_qualifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->text('qual_name')->nullable();
            $table->text('qual_code')->nullable();
            $table->text('qual_sub_sector')->nullable();
            $table->text('qual_trainee_to_trainer_ratio')->nullable();
            $table->text('qual_associated_classroom')->nullable();
            $table->text('qual_associated_lab')->nullable();
            $table->text('qual_no_of_parallel_batch')->nullable();
            $table->text('qual_trainer_ava')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_qualifications');
    }
};
