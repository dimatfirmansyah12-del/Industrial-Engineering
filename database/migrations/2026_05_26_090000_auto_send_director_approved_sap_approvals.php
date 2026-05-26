<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $approvedApprovals = DB::table('sap_approvals')
            ->where('approval_status', 'Approved for Purchasing')
            ->get();

        foreach ($approvedApprovals as $approval) {
            DB::table('sap_approvals')
                ->where('id', $approval->id)
                ->update([
                    'approval_status' => 'Sent to Purchasing',
                    'sent_to_purchasing_by' => $approval->sent_to_purchasing_by ?: 'System',
                    'sent_to_purchasing_at' => $approval->sent_to_purchasing_at ?: now(),
                    'updated_at' => now(),
                ]);

            if (! $approval->sap_number) {
                continue;
            }

            $hasPurchaseRequest = DB::table('purchase_requests')
                ->where('ie_request_id', $approval->ie_request_id)
                ->exists();

            $prNumberUsed = DB::table('purchase_requests')
                ->where('pr_number', $approval->sap_number)
                ->where('ie_request_id', '!=', $approval->ie_request_id)
                ->exists();

            if (! $hasPurchaseRequest && ! $prNumberUsed) {
                $materialTotal = DB::table('request_materials')
                    ->where('ie_request_id', $approval->ie_request_id)
                    ->sum('total_price');

                DB::table('purchase_requests')->insert([
                    'ie_request_id' => $approval->ie_request_id,
                    'pr_number' => $approval->sap_number,
                    'pr_date' => $approval->sap_input_date,
                    'total_budget' => $approval->purchase_value ?? $materialTotal,
                    'pr_status' => 'Approved',
                    'requested_by' => $approval->sap_input_by,
                    'approved_by' => $approval->director_by,
                    'approved_at' => $approval->director_at,
                    'note' => $approval->sap_description ?: 'Dibuat otomatis setelah Direktur approve SAP / PR Approval.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('ie_requests')
                ->where('id', $approval->ie_request_id)
                ->where('status', 'Approved for Purchasing')
                ->update([
                    'status' => 'Sent to Purchasing',
                    'updated_at' => now(),
                ]);
        }

        Schema::table('sap_approvals', function (Blueprint $table) {
            $table->dropColumn(['plant', 'realised_strategy']);
        });
    }

    public function down(): void
    {
        Schema::table('sap_approvals', function (Blueprint $table) {
            $table->string('plant')->nullable()->after('ie_request_id');
            $table->string('realised_strategy')->nullable()->after('purchase_value');
        });
    }
};
