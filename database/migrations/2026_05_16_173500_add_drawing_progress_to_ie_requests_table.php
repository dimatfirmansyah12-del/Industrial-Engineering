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
            $table->string('drawing_status')->default('Not Started')->after('drawing_file');
            $table->timestamp('drawing_started_at')->nullable()->after('drawing_status');
            $table->timestamp('drawing_finished_at')->nullable()->after('drawing_started_at');
            $table->text('drawing_revision_note')->nullable()->after('drawing_finished_at');
            $table->text('drawing_note')->nullable()->after('drawing_revision_note');
            $table->string('assigned_drafter')->nullable()->after('drawing_note');
        });

        DB::table('ie_requests')
            ->whereNull('drawing_status')
            ->update(['drawing_status' => 'Not Started']);
    }

    public function down(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->dropColumn([
                'drawing_status',
                'drawing_started_at',
                'drawing_finished_at',
                'drawing_revision_note',
                'drawing_note',
                'assigned_drafter',
            ]);
        });
    }
};
