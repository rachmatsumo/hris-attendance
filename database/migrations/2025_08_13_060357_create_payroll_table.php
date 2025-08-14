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
        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            
            // Attendance Summary
            $table->integer('total_working_days');
            $table->integer('total_present_days');
            $table->integer('total_late_days');
            $table->integer('total_absent_days');
            $table->decimal('total_working_hours', 6, 2)->default(0);
            $table->decimal('total_overtime_hours', 6, 2)->default(0);
            
            // Salary Calculation
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('meal_allowance_total', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'finalized', 'paid'])->default('draft');
            $table->datetime('finalized_at')->nullable();
            $table->datetime('paid_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
