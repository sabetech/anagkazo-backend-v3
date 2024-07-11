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
        Schema::create('fellowship_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained("students");
            $table->dateTime('service_date');
            $table->unsignedInteger('attendance')->nullable();
            $table->unsignedDecimal('offering')->nullable();
            $table->unsignedDecimal('foreign_offering')->nullable();
            $table->text('cancel_service_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fellowship_service');
    }
};
