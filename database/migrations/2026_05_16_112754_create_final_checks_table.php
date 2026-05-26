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
        Schema::create('final_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ie_request_id')->constrained('ie_requests')->cascadeOnDelete();
            $table->foreignId('workshop_schedule_id')->nullable()->constrained('workshop_schedules')->nullOnDelete();
            $table->date('check_date')->nullable();
            $table->string('checked_by')->nullable();
            $table->string('check_status')->default('Waiting Check');
            $table->string('result_status')->nullable();
            $table->text('problem_note')->nullable();
            $table->text('correction_note')->nullable();
            $table->text('final_note')->nullable();
            $table->string('evidence_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_checks');
    }
};
