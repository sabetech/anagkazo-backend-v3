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
        //
        Schema::table('student_attendance', function (Blueprint $table) {
            $table->dropColumn('student_admission_number');
            $table->dropColumn('class_id');
            $table->dropColumn('todays_qr_salt');
            $table->dropColumn('service_type');
            $table->dropColumn('todays_qr_salt');
            $table->dropColumn('attendance_status');
            $table->renameColumn('date_time', 'date');
            $table->time('time')->after('date');
            $table->time('late_condition')->after('time')->nullable();
        });

        Schema::rename('student_attendance', 'anagkazo_attendance');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
