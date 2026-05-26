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
            $table->string('progress_status')->default('Not Started')->after('reschedule_reason');
            $table->integer('progress_percentage')->default(0)->after('progress_status');
            $table->text('progress_note')->nullable()->after('progress_percentage');
            $table->text('problem_note')->nullable()->after('progress_note');
            $table->timestamp('started_at')->nullable()->after('problem_note');
            $table->timestamp('finished_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'progress_status',
                'progress_percentage',
                'progress_note',
                'problem_note',
                'started_at',
                'finished_at',
            ]);
        });
    }
};
