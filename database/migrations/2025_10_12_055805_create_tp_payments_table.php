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
        Schema::create('tp_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->string('payment_method')->nullable();
            $table->string('payment_details')->nullable();
            $table->text('payment_type')->nullable();
            $table->date('payment_date')->nullable();
            $table->double('amount')->nullable();
            $table->text('payment_file')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_payments');
    }
};
