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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('nickname');
            }

            if (! Schema::hasColumn('users', 'whatsapp_number')) {
                $table->string('whatsapp_number', 30)->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'home_address')) {
                $table->text('home_address')->nullable()->after('whatsapp_number');
            }

            if (! Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->default('Business Administration & Management')->after('home_address');
            }

            if (! Schema::hasColumn('users', 'academic_level')) {
                $table->enum('academic_level', ['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'])->default('ND1')->after('department');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['gender', 'whatsapp_number', 'home_address', 'department', 'academic_level'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
