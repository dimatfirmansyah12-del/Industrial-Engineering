<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sap_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ie_request_id')->unique()->constrained('ie_requests')->cascadeOnDelete();
            $table->string('sap_number')->nullable();
            $table->date('sap_input_date')->nullable();
            $table->string('sap_file')->nullable();
            $table->text('sap_note')->nullable();
            $table->string('sap_input_by')->nullable();
            $table->timestamp('sap_input_at')->nullable();
            $table->string('approval_status')->default('Waiting SAP Input');
            $table->string('section_head_status')->default('Waiting');
            $table->string('section_head_by')->nullable();
            $table->timestamp('section_head_at')->nullable();
            $table->text('section_head_note')->nullable();
            $table->text('section_head_rejected_reason')->nullable();
            $table->string('division_head_status')->default('Waiting');
            $table->string('division_head_by')->nullable();
            $table->timestamp('division_head_at')->nullable();
            $table->text('division_head_note')->nullable();
            $table->text('division_head_rejected_reason')->nullable();
            $table->string('sent_to_purchasing_by')->nullable();
            $table->timestamp('sent_to_purchasing_at')->nullable();
            $table->timestamps();
        });

        $existingPrRequests = DB::table('purchase_requests')
            ->select('ie_request_id')
            ->distinct()
            ->pluck('ie_request_id');

        foreach ($existingPrRequests as $ieRequestId) {
            DB::table('sap_approvals')->insert([
                'ie_request_id' => $ieRequestId,
                'approval_status' => 'Sent to Purchasing',
                'section_head_status' => 'Approved',
                'division_head_status' => 'Approved',
                'sap_note' => 'Migrated from existing purchase request data.',
                'sent_to_purchasing_by' => 'System',
                'sent_to_purchasing_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sap_approvals');
    }
};
