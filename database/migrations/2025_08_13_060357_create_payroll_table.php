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
            $table->enum('payroll_type', ['regular','bonus','allowance']);
             
            $table->integer('total_working_days');
            $table->integer('total_present_days');
            $table->integer('total_late_days');
            $table->integer('total_absent_days');
            $table->decimal('total_working_hours', 6, 2)->default(0);
            $table->decimal('total_overtime_hours', 6, 2)->default(0);

            $table->json('incomes_data')->nullable();
            $table->decimal('incomes_total', 12, 2)->default(0);
            $table->json('deductions_data')->nullable();
            $table->decimal('deductions_total', 12, 2)->default(0); 
            $table->decimal('net_salary', 12, 2)->default(0); 
            
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'finalized', 'paid'])->default('draft');
            $table->datetime('finalized_at')->nullable();
            $table->datetime('paid_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'month', 'year', 'payroll_type']);
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
