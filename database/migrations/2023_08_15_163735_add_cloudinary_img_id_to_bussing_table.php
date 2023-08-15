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
        Schema::table('bussing_data', function (Blueprint $table) {
            //
            $table->string('cloudinary_img_id')->nullable()->after('number_bussed');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bussing', function (Blueprint $table) {
            //
        });
    }
};
