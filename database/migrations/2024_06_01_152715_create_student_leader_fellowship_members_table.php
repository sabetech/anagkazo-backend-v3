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
        //REWORK THIS ... SOON not LATER!!!
        // Schema::create('student_leader_fellowship', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId("bacenta_leader_student_id")->constrained("students");
        //     $table->foreignId("fellowship_leader_student_id")->constrained("students");
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_leader_fellowship');
    }
};
