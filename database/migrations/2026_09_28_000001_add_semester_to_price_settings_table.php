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
        Schema::table('price_settings', function (Blueprint $table) {
            $table->enum('semester', ['First', 'Second'])->default('First')->after('level_id')->index();
            $table->dropUnique('price_settings_name_session_level_unique');
            $table->unique(['name', 'academic_session_id', 'level_id', 'semester'], 'price_settings_name_session_level_semester_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_settings', function (Blueprint $table) {
            $table->dropUnique('price_settings_name_session_level_semester_unique');
            $table->unique(['name', 'academic_session_id', 'level_id'], 'price_settings_name_session_level_unique');
            $table->dropIndex(['semester']);
            $table->dropColumn('semester');
        });
    }
};
