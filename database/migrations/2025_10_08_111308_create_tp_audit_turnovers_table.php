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
        Schema::create('tp_audit_turnovers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');
            $table->text('financial_yrs')->nullable();
            $table->text('turn_over')->nullable();
            $table->text('report_file')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_audit_turnovers');
    }
};
