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
        Schema::create('co_e_funding_agencies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->text('agency_name')->nullable();
            $table->text('amount')->nullable();
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
        Schema::dropIfExists('co_e_funding_agencies');
    }
};
