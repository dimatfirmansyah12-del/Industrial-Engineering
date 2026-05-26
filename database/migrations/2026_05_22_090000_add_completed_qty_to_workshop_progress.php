<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshop_schedules', function (Blueprint $table) {
            $table->unsignedInteger('completed_qty')->default(0)->after('progress_percentage');
        });

        Schema::table('workshop_progress_logs', function (Blueprint $table) {
            $table->unsignedInteger('completed_qty')->nullable()->after('progress_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('workshop_progress_logs', function (Blueprint $table) {
            $table->dropColumn('completed_qty');
        });

        Schema::table('workshop_schedules', function (Blueprint $table) {
            $table->dropColumn('completed_qty');
        });
    }
};
