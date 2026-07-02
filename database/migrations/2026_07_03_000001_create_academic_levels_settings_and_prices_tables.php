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
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->unique();
            $table->unsignedSmallInteger('starts_at_year');
            $table->unsignedSmallInteger('ends_at_year');
            $table->enum('is_current', ['Yes', 'No'])->default('No')->index();
            $table->enum('is_active', ['Yes', 'No'])->default('Yes')->index();
            $table->timestamps();
        });

        Schema::create('levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('is_active', ['Yes', 'No'])->default('Yes')->index();
            $table->timestamps();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('value')->nullable();
            $table->string('setting_group', 80)->default('general')->index();
            $table->enum('active', ['Yes', 'No'])->default('Yes')->index();
            $table->timestamps();
        });

        Schema::create('price_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedInteger('amount')->default(0);
            $table->unsignedInteger('academic_session_id')->index();
            $table->unsignedInteger('level_id')->index();
            $table->enum('is_active', ['Yes', 'No'])->default('Yes')->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['name', 'academic_session_id', 'level_id'], 'price_settings_name_session_level_unique');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('level_id')->references('id')->on('levels')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_settings');
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('academic_sessions');
    }
};
