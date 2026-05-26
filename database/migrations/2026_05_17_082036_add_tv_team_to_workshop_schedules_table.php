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
        Schema::table('workshop_schedules', function (Blueprint $table) {
            $table->json('tv_team_members')->nullable()->after('schedule_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_schedules', function (Blueprint $table) {
            $table->dropColumn('tv_team_members');
        });
    }
};
