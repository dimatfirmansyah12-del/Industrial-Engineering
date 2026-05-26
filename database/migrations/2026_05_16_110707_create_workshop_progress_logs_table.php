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
        Schema::create('workshop_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_schedule_id')->constrained('workshop_schedules')->cascadeOnDelete();
            $table->foreignId('ie_request_id')->constrained('ie_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('progress_status');
            $table->integer('progress_percentage')->default(0);
            $table->text('note')->nullable();
            $table->text('problem_note')->nullable();
            $table->string('photo_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_progress_logs');
    }
};
