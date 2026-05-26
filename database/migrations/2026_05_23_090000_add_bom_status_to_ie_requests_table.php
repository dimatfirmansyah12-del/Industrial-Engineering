<?php

use App\Models\IeRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->string('bom_status')->default(IeRequest::BOM_NO_BOM)->after('drawing_file');
            $table->string('bom_submitted_by')->nullable()->after('bom_status');
            $table->timestamp('bom_submitted_at')->nullable()->after('bom_submitted_by');
            $table->text('bom_revision_note')->nullable()->after('bom_submitted_at');
        });

        DB::table('ie_requests')
            ->whereIn('id', DB::table('request_materials')->select('ie_request_id')->distinct())
            ->update([
                'bom_status' => IeRequest::BOM_SUBMITTED,
                'bom_submitted_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->dropColumn([
                'bom_status',
                'bom_submitted_by',
                'bom_submitted_at',
                'bom_revision_note',
            ]);
        });
    }
};
