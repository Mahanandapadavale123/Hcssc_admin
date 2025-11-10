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


        Schema::create('tc_galleries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tp_id')->constrained('t_p_users')->onDelete('cascade');

            $table->text('image_outarea')->nullable();
            $table->text('image_approach_road')->nullable();
            $table->text('image_tc')->nullable();
            $table->text('image_tc_front')->nullable();
            $table->text('image_tc_back')->nullable();
            $table->text('image_tc_left')->nullable();
            $table->text('image_tc_right')->nullable();
            $table->text('image_biometric_device')->nullable();
            $table->text('image_classroom')->nullable();

            $table->text('image_lab')->nullable();
            $table->text('image_firstaid')->nullable();
            $table->text('image_fire')->nullable();
            $table->text('image_water')->nullable();
            $table->text('image_insecption_card')->nullable();
            $table->text('image_washroom')->nullable();
            $table->text('image_reception')->nullable();
            $table->text('image_placementcell')->nullable();
            $table->text('image_counselling')->nullable();
            $table->text('image_library')->nullable();

            $table->text('image_office')->nullable();
            $table->text('image_pantry')->nullable();
            $table->text('image_parking')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_galleries');
    }
};
