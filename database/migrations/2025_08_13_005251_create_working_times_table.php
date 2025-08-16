<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('working_times', function (Blueprint $table) {
            $table->id();
            $table->string('name');  
            $table->string('code', 10)->unique(); // Unique code for each working time
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('end_next_day')->default(false); // true jika berakhir keesokan harinya
            $table->time('break_start_time')->nullable();
            $table->time('break_end_time')->nullable();
            $table->integer('late_tolerance_minutes')->default(15);
            $table->boolean('is_location_limited')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_time');
    }
};
