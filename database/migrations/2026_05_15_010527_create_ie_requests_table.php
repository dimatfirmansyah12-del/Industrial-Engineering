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
       Schema::create('ie_requests', function (Blueprint $table) {
    $table->id();

    $table->string('request_number')->unique();
    $table->date('request_date');
    $table->string('requester_name');
    $table->string('department');
    $table->string('line_area')->nullable();
    $table->string('request_type');
    $table->text('description');
    $table->string('priority')->default('Medium');
    $table->string('status')->default('Request Submitted');
    $table->date('target_date')->nullable();

    $table->string('pic_drafter')->nullable();
    $table->string('pic_workshop')->nullable();

    $table->text('notes')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ie_requests');
    }
};
