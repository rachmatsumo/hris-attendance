<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; 

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('working_times', function (Blueprint $table) {
            $table->string('color', 15) // untuk HEX color, misal #FF0000
                  ->nullable()
                  ->after('late_tolerance_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('working_times', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};

