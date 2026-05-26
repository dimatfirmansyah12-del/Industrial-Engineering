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
        Schema::table('request_materials', function (Blueprint $table) {
            $table->decimal('arrived_qty', 12, 2)->default(0)->after('note');
            $table->string('arrival_status')->default('Waiting Material')->after('arrived_qty');
            $table->date('arrival_date')->nullable()->after('arrival_status');
            $table->text('arrival_note')->nullable()->after('arrival_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_materials', function (Blueprint $table) {
            $table->dropColumn([
                'arrived_qty',
                'arrival_status',
                'arrival_date',
                'arrival_note',
            ]);
        });
    }
};
