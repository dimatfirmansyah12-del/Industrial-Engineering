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
            $table->string('plant')->nullable()->after('ie_request_id');
            $table->text('sap_description')->nullable()->after('plant');
            $table->decimal('purchase_value', 15, 2)->nullable()->after('sap_number');
            $table->string('realised_strategy')->nullable()->after('purchase_value');
        });

        $materialTotals = DB::table('request_materials')
            ->select('ie_request_id', DB::raw('COALESCE(SUM(total_price), 0) as total_price'))
            ->groupBy('ie_request_id')
            ->pluck('total_price', 'ie_request_id');

        foreach ($materialTotals as $ieRequestId => $totalPrice) {
            DB::table('sap_approvals')
                ->where('ie_request_id', $ieRequestId)
                ->whereNull('purchase_value')
                ->update(['purchase_value' => $totalPrice]);
        }
    }

    public function down(): void
    {
        Schema::table('sap_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'plant',
                'sap_description',
                'purchase_value',
                'realised_strategy',
            ]);
        });
    }
};
