<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')
                ->constrained('positions')
                ->onDelete('cascade');

            $table->decimal('basic_salary', 15)->default(0);
            $table->decimal('transport_allowance', 15)->default(0);
            $table->decimal('meal_allowance', 15)->default(0);
            $table->decimal('daily_salary', 15)->default(0);
            $table->decimal('overtime_rate', 15)->default(0);
            $table->decimal('bonus', 15)->default(0);
            $table->decimal('deductions', 15)->default(0);

            // Kalau mau simpan net_salary langsung
            $table->decimal('net_salary', 15, 2)->default(0);

            $table->date('effective_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
