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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('first_name');
            $table->string('last_name');

            $table->string('emp_code')->nullable();
            $table->string('designation')->nullable();
            $table->foreignId('dept_id')->constrained('departments')->onDelete('cascade');

            $table->enum('emp_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_leaving')->nullable();
            $table->string('work_location')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();

            $table->text('full_address')->nullable();
            $table->string('emergency_contact_name')->nullable();

            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->string('bank_account_no', 100)->nullable();
            $table->string('ifsc_code', 20)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('aadhaar_no', 20)->nullable();

            $table->enum('emp_status', ['active', 'probation', 'resigned', 'terminated', 'inactive'])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
