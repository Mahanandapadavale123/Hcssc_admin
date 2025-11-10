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
        Schema::create('tc_trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->text('qual_code')->nullable();
            $table->text('qCode')->nullable();
            $table->text('trainerName')->nullable();
            $table->text('trainerExp')->nullable();
            $table->text('trainer')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_trainers');
    }
};
