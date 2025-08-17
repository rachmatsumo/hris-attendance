<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Levels
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');  
            $table->integer('grade');   
            $table->timestamps();
        });

        // 2. Update Tabel Positions: tambah kolom level_id
        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('level_id')->nullable()->after('department_id')->constrained('levels')->onDelete('set null');
        });

        // 3. Tabel Incomes
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('name');           
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->enum('category', ['base','daily']);  
            $table->decimal('value', 15, 2);   
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Tabel Deductions
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Nama potongan, misal "BPJS", "Loan"
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->decimal('value', 15, 2);   // Nilai nominal atau persentase
            $table->enum('type_value', ['fixed','percent'])->default('fixed'); // fixed atau percent
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('positions', function(Blueprint $table) {
            $table->dropForeign(['level_id']);
            $table->dropColumn('level_id');
        });

        Schema::dropIfExists('deductions');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('levels');
    }
};