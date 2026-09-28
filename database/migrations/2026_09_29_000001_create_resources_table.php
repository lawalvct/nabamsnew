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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('category', 60)->nullable()->index();
            $table->unsignedInteger('level_id')->nullable()->index();
            $table->enum('access', ['free', 'paid'])->default('free')->index();
            $table->unsignedInteger('price')->default(0);
            $table->string('file_path');
            $table->string('file_extension', 10);
            $table->string('file_mime', 120);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->enum('is_published', ['Yes', 'No'])->default('Yes')->index();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels')->nullOnDelete()->cascadeOnUpdate();
        });

        // Resource purchases go through the same manual payment + verification flow as dues.
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('type', ['dues', 'resource'])->default('dues')->after('reference')->index();
            $table->foreignId('resource_id')->nullable()->after('type')->constrained('resources')->nullOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('academic_session_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('resource_id');
            $table->dropColumn('type');
        });

        Schema::dropIfExists('resources');
    }
};
