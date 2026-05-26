<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->string('memo_status')->default('Waiting Approval')->after('memo_file');
            $table->string('memo_approved_by')->nullable()->after('memo_status');
            $table->timestamp('memo_approved_at')->nullable()->after('memo_approved_by');
            $table->text('memo_rejected_reason')->nullable()->after('memo_approved_at');
            $table->text('memo_approval_note')->nullable()->after('memo_rejected_reason');
        });

        DB::table('ie_requests')
            ->whereNull('memo_status')
            ->update(['memo_status' => 'Waiting Approval']);
    }

    public function down(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->dropColumn([
                'memo_status',
                'memo_approved_by',
                'memo_approved_at',
                'memo_rejected_reason',
                'memo_approval_note',
            ]);
        });
    }
};
