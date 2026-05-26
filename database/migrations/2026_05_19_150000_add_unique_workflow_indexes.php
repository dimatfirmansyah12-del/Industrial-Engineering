<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('final_checks', function (Blueprint $table) {
            $table->unique('ie_request_id', 'final_checks_ie_request_id_unique');
        });

        Schema::table('handovers', function (Blueprint $table) {
            $table->unique('ie_request_id', 'handovers_ie_request_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropUnique('handovers_ie_request_id_unique');
        });

        Schema::table('final_checks', function (Blueprint $table) {
            $table->dropUnique('final_checks_ie_request_id_unique');
        });
    }
};
