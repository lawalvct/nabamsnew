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
        Schema::create('election_vote_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('academic_session_id')->index();
            $table->foreignId('position_id')->constrained('election_positions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('aspirant_id')->constrained('election_aspirants')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->integer('adjustment');
            $table->unsignedInteger('before_total')->default(0);
            $table->unsignedInteger('after_total')->default(0);
            $table->text('reason');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->index(['academic_session_id', 'position_id', 'aspirant_id'], 'election_adjustment_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_vote_adjustments');
    }
};
