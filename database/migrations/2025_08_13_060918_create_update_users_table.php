<?php
// database/migrations/2024_01_01_000002_update_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->unique()->after('id');
            $table->enum('role', ['admin', 'hr', 'employee'])->default('employee')->after('email');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete()->after('role');
            $table->string('phone')->nullable()->after('department_id');
            $table->date('join_date')->nullable()->after('phone');
            $table->decimal('salary_per_day', 10, 2)->default(0)->after('join_date');
            $table->decimal('meal_allowance', 8, 2)->default(0)->after('salary_per_day');
            $table->string('profile_photo')->nullable()->after('meal_allowance');
            $table->boolean('is_active')->default(true)->after('profile_photo');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'employee_id', 'role', 'department_id', 'phone', 
                'join_date', 'salary_per_day', 'meal_allowance', 
                'profile_photo', 'is_active'
            ]);
        });
    }
};