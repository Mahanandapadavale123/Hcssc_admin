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
        Schema::create('tp_training_infrastructures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->enum('type', ['class', 'lab', 'hybrid'])->default('class');
            $table->text('room_name')->nullable();
            $table->text('carpet_area')->nullable();
            $table->text('furnished')->nullable();
            $table->text('infr_remark')->nullable();
            $table->text('qual_name')->nullable();
            $table->text('qual_code')->nullable();
            $table->text('qual_sub_code')->nullable();
            $table->text('qual_trainee_to_trainer_ratio')->nullable();
            $table->text('qual_no_of_parallel_batch')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_training_infrastructures');
    }
};
