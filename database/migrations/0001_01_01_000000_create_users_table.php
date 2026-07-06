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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 50);
            $table->string('lastname', 50)->nullable();
            $table->string('nickname', 50)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('email', 60)->unique();
            $table->string('password');
            $table->string('matno', 30)->nullable()->unique();
            $table->string('phone', 30);
            $table->string('whatsapp_number', 30)->nullable();
            $table->text('home_address')->nullable();
            $table->string('department', 100)->default('Business Administration & Management');
            $table->enum('academic_level', ['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'])->default('ND1');
            $table->unsignedInteger('level_id')->default(1)->index();
            $table->enum('member_type', ['Regular', 'Alumni', 'Part-time'])->default('Regular');
            $table->text('expectation_msg')->nullable();
            $table->string('session_start', 15)->nullable();
            $table->string('session_end', 15)->nullable();
            $table->enum('is_active', ['Yes', 'No'])->default('No')->index();
            $table->enum('is_ban', ['Yes', 'No'])->default('No')->index();
            $table->enum('fee_paid', ['Yes', 'No'])->default('No');
            $table->enum('role', ['Member', 'Admin', 'Lecturer'])->default('Member')->index();
            $table->text('bio')->nullable();
            $table->date('dob')->nullable();
            $table->string('image', 100)->nullable();
            $table->string('facebook_link', 100)->nullable();
            $table->string('x_link', 100)->nullable();
            $table->string('linkedin_link', 100)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedInteger('user_role_id')->default(2)->index();
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
