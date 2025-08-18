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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_schedule_id')
                  ->nullable() // biar tidak wajib diisi saat ada data lama
                  ->constrained('work_schedules') // relasi ke work_schedules.id
                  ->cascadeOnDelete();
            $table->date('date');
            
            // Clock In Data
            $table->datetime('clock_in_time')->nullable();
            $table->decimal('clock_in_lat', 10, 8)->nullable();
            $table->decimal('clock_in_lng', 11, 8)->nullable();
            $table->string('clock_in_photo')->nullable();
            $table->text('clock_in_notes')->nullable();
            
            // Clock Out Data
            $table->datetime('clock_out_time')->nullable();
            $table->decimal('clock_out_lat', 10, 8)->nullable();
            $table->decimal('clock_out_lng', 11, 8)->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->text('clock_out_notes')->nullable();
            
            // Status and Additional Info
            $table->enum('status', ['present', 'late', 'absent'])->default('absent');
            $table->integer('late_minutes')->default(0);
            $table->decimal('working_hours', 4, 2)->default(0);
            // $table->decimal('overtime_hours', 4, 2)->default(0);
            $table->integer('overtime_salary')->default(0);
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances'); 
    }
};
