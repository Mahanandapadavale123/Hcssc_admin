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
        Schema::create('tp_trainer_and_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->text('qual_code')->nullable();
            $table->text('tool_id')->nullable();
            $table->text('short_qul_code')->nullable();
            $table->text('short_tool_name')->nullable();
            $table->text('qty_avl')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_trainer_and_equipment');
    }
};
