<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sap_approvals', function (Blueprint $table) {
            $table->string('director_status')->default('Waiting')->after('division_head_rejected_reason');
            $table->string('director_by')->nullable()->after('director_status');
            $table->timestamp('director_at')->nullable()->after('director_by');
            $table->text('director_note')->nullable()->after('director_at');
            $table->text('director_rejected_reason')->nullable()->after('director_note');
        });

        DB::table('sap_approvals')
            ->where('approval_status', 'Waiting SAP Input')
            ->update(['approval_status' => 'Waiting PR Input']);

        DB::table('sap_approvals')
            ->where('approval_status', 'Waiting Section Head Approval')
            ->update(['approval_status' => 'Waiting Atasan IE Approval']);

        DB::table('sap_approvals')
            ->whereIn('approval_status', ['Approved for Purchasing', 'Sent to Purchasing'])
            ->update(['director_status' => 'Approved']);

        DB::table('ie_requests')
            ->where('status', 'Waiting SAP Input')
            ->update(['status' => 'Waiting PR Input']);

        DB::table('ie_requests')
            ->where('status', 'Waiting Section Head Approval')
            ->update(['status' => 'Waiting Atasan IE Approval']);

        DB::table('ie_requests')
            ->where('status', 'SAP Approval Rejected')
            ->update(['status' => 'PR Approval Rejected']);
    }

    public function down(): void
    {
        DB::table('sap_approvals')
            ->where('approval_status', 'Waiting PR Input')
            ->update(['approval_status' => 'Waiting SAP Input']);

        DB::table('sap_approvals')
            ->where('approval_status', 'Waiting Atasan IE Approval')
            ->update(['approval_status' => 'Waiting Section Head Approval']);

        DB::table('ie_requests')
            ->where('status', 'Waiting PR Input')
            ->update(['status' => 'Waiting SAP Input']);

        DB::table('ie_requests')
            ->where('status', 'Waiting Atasan IE Approval')
            ->update(['status' => 'Waiting Section Head Approval']);

        DB::table('ie_requests')
            ->where('status', 'PR Approval Rejected')
            ->update(['status' => 'SAP Approval Rejected']);

        Schema::table('sap_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'director_status',
                'director_by',
                'director_at',
                'director_note',
                'director_rejected_reason',
            ]);
        });
    }
};
