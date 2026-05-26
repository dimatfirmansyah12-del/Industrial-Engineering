<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->string('memo_file')->nullable()->after('notes');
            $table->string('drawing_file')->nullable()->after('memo_file');
        });
    }

    public function down(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->dropColumn(['memo_file', 'drawing_file']);
        });
    }
};