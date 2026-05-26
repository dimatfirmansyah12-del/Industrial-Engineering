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
        Schema::create('workshop_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ie_request_id')->unique()->constrained('ie_requests')->cascadeOnDelete();
            $table->string('schedule_number')->unique();
            $table->date('planned_start_date')->nullable();
            $table->date('planned_finish_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_finish_date')->nullable();
            $table->string('pic_workshop')->nullable();
            $table->integer('estimated_duration')->nullable();
            $table->string('schedule_status')->default('Scheduled');
            $table->text('schedule_note')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_schedules');
    }
};
