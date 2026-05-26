<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_materials', function (Blueprint $table) {
            $table->string('material_category')->nullable()->after('ie_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('request_materials', function (Blueprint $table) {
            $table->dropColumn('material_category');
        });
    }
};
