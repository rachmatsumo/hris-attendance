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
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete()->after('role');
            $table->string('phone')->nullable()->after('position_id');
            $table->date('join_date')->nullable()->after('phone');
            $table->string('profile_photo')->nullable()->after('join_date');
            $table->enum('gender', ['male','female'])->nullable()->after('profile_photo');
            $table->boolean('is_active')->default(true)->after('gender');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) { 
            $table->dropColumn('position_id');
            $table->dropColumn([
                'employee_id', 'role', 'position_id', 'phone', 
                'join_date','profile_photo', 'is_active', 'gender'
            ]);
        });
    }
};