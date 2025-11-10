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
        Schema::create('end_user_charges', function (Blueprint $table) {
            $table->id();

            $table->string('user_type')->nullable();
            $table->enum('payment_type', ['initial_payment', 'final_payment'])->default('initial_payment');
            $table->string('category')->nullable();
            $table->string('description');
            $table->double('amount');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('end_user_charges');
    }
};
