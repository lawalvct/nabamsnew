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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->char('reference', 8)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('academic_session_id')->index();
            // Null semester means the payment covers the whole session.
            $table->enum('semester', ['First', 'Second'])->nullable()->index();
            $table->unsignedInteger('level_id')->nullable();
            $table->string('level_name', 20)->nullable();
            $table->json('items')->nullable();
            $table->unsignedInteger('amount_due')->default(0);
            $table->unsignedInteger('amount_paid')->nullable();
            $table->unsignedInteger('bank_account_id')->nullable();
            $table->json('bank_snapshot')->nullable();
            $table->string('payer_name', 150)->nullable();
            $table->date('paid_at')->nullable();
            $table->string('evidence_path')->nullable();
            $table->enum('status', ['awaiting_payment', 'pending', 'approved', 'rejected'])->default('awaiting_payment')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('reviewer_name', 150)->nullable();
            $table->string('rejection_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'academic_session_id', 'semester', 'status'], 'payments_user_period_status_index');
            $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
