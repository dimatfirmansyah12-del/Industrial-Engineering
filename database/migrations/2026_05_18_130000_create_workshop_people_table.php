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
        Schema::create('workshop_people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->unsignedTinyInteger('photo_position_x')->default(50);
            $table->unsignedTinyInteger('photo_position_y')->default(50);
            $table->unsignedTinyInteger('photo_zoom')->default(100);
            $table->text('current_work')->nullable();
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->text('progress_note')->nullable();
            $table->string('status')->default('Active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_people');
    }
};
