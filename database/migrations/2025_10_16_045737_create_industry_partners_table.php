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
        Schema::create('industry_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->string('party_name')->nullable();
            $table->text('dt_of_partner')->nullable();
            $table->string('purpose')->nullable();
            $table->string('remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_partners');
    }
};
