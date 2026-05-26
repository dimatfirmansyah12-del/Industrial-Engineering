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
        Schema::create('request_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ie_request_id')->constrained('ie_requests')->cascadeOnDelete();
            $table->string('material_name');
            $table->text('specification')->nullable();
            $table->decimal('qty', 12, 2)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('estimated_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->string('material_status')->default('Need Purchase');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_materials');
    }
};
