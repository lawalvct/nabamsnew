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
        Schema::create('election_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('academic_session_id');
            $table->string('name', 100);
            $table->unsignedInteger('form_amount')->default(0);
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->enum('is_active', ['Yes', 'No'])->default('Yes')->index();
            $table->timestamps();

            $table->unique(['academic_session_id', 'name']);
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('election_aspirants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('academic_session_id');
            $table->string('name', 100);
            $table->text('manifesto')->nullable();
            $table->string('photo', 150)->nullable();
            $table->enum('screening_status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->timestamps();

            $table->unique(['user_id', 'academic_session_id']);
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('election_aspirant_position', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_aspirant_id')->constrained('election_aspirants')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('election_position_id')->constrained('election_positions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('payment_status', ['pending', 'approved'])->default('pending')->index();
            $table->enum('result_status', ['pending', 'won', 'lost'])->default('pending')->index();
            $table->timestamps();

            $table->unique(['election_aspirant_id', 'election_position_id'], 'election_aspirant_position_unique');
        });

        Schema::create('election_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('academic_session_id');
            $table->foreignId('position_id')->constrained('election_positions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('aspirant_id')->constrained('election_aspirants')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'position_id']);
            $table->unique(['user_id', 'position_id', 'aspirant_id'], 'election_vote_user_position_aspirant_unique');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_votes');
        Schema::dropIfExists('election_aspirant_position');
        Schema::dropIfExists('election_aspirants');
        Schema::dropIfExists('election_positions');
    }
};
