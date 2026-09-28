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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bank_name', 100);
            $table->string('account_name', 150);
            $table->string('account_number', 20);
            $table->string('instructions', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('is_active', ['Yes', 'No'])->default('Yes')->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['bank_name', 'account_number'], 'bank_accounts_bank_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
