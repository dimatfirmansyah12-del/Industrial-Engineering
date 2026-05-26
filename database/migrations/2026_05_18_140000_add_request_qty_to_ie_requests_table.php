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
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->unsignedInteger('request_qty')->default(1)->after('request_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ie_requests', function (Blueprint $table) {
            $table->dropColumn('request_qty');
        });
    }
};
