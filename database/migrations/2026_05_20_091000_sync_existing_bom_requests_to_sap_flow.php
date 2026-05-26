<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $requestIds = DB::table('ie_requests')
            ->where('drawing_status', 'Done')
            ->whereNotIn('status', ['Completed', 'Closed'])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('request_materials')
                    ->whereColumn('request_materials.ie_request_id', 'ie_requests.id');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('purchase_requests')
                    ->whereColumn('purchase_requests.ie_request_id', 'ie_requests.id');
            })
            ->pluck('id');

        if ($requestIds->isNotEmpty()) {
            DB::table('ie_requests')
                ->whereIn('id', $requestIds)
                ->update([
                    'status' => 'Waiting SAP Input',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Status sync is intentionally not reversed.
    }
};
