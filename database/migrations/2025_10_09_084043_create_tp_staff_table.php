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
        Schema::create('tp_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->enum('staff_type', ['Owner', 'SPOC', 'Management', 'Trainer'])->default('Management');

            $table->text('name');
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->text('alt_phone')->nullable();
            $table->text('education')->nullable();
            $table->text('designation')->nullable();
            $table->string('experience')->nullable();
            $table->text('resume')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_staff');
    }
};
