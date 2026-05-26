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
        Schema::create('handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ie_request_id')->constrained('ie_requests')->cascadeOnDelete();
            $table->foreignId('final_check_id')->nullable()->constrained('final_checks')->nullOnDelete();
            $table->string('handover_number')->unique();
            $table->date('handover_date')->nullable();
            $table->string('handed_over_by')->nullable();
            $table->string('received_by')->nullable();
            $table->string('receiver_department')->nullable();
            $table->string('handover_status')->default('Waiting Handover');
            $table->text('handover_note')->nullable();
            $table->text('receiver_note')->nullable();
            $table->string('evidence_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handovers');
    }
};
