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
        Schema::create('coe_reserches', function (Blueprint $table) {
            $table->id();
             $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->text('subject')->nullable();
            $table->text(column: 'description')->nullable();
            $table->text('year')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coe_reserches');
    }
};
