<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname', 50)->default('')->after('id');
            }

            if (! Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname', 50)->nullable()->after('firstname');
            }

            if (! Schema::hasColumn('users', 'nickname')) {
                $table->string('nickname', 50)->nullable()->after('lastname');
            }

            if (! Schema::hasColumn('users', 'matno')) {
                $table->string('matno', 30)->nullable()->unique()->after('password');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->default('')->after('matno');
            }

            if (! Schema::hasColumn('users', 'level_id')) {
                $table->unsignedInteger('level_id')->default(1)->index()->after('phone');
            }

            if (! Schema::hasColumn('users', 'member_type')) {
                $table->enum('member_type', ['Regular', 'Alumni', 'Part-time'])->default('Regular')->after('level_id');
            }

            if (! Schema::hasColumn('users', 'expectation_msg')) {
                $table->text('expectation_msg')->nullable()->after('member_type');
            }

            if (! Schema::hasColumn('users', 'session_start')) {
                $table->string('session_start', 15)->nullable()->after('expectation_msg');
            }

            if (! Schema::hasColumn('users', 'session_end')) {
                $table->string('session_end', 15)->nullable()->after('session_start');
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->enum('is_active', ['Yes', 'No'])->default('No')->index()->after('updated_at');
            }

            if (! Schema::hasColumn('users', 'is_ban')) {
                $table->enum('is_ban', ['Yes', 'No'])->default('No')->index()->after('is_active');
            }

            if (! Schema::hasColumn('users', 'fee_paid')) {
                $table->enum('fee_paid', ['Yes', 'No'])->default('No')->after('is_ban');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['Member', 'Admin', 'Lecturer'])->default('Member')->index()->after('fee_paid');
            }

            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('role');
            }

            if (! Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('bio');
            }

            if (! Schema::hasColumn('users', 'image')) {
                $table->string('image', 100)->nullable()->after('dob');
            }

            if (! Schema::hasColumn('users', 'facebook_link')) {
                $table->string('facebook_link', 100)->nullable()->after('image');
            }

            if (! Schema::hasColumn('users', 'x_link')) {
                $table->string('x_link', 100)->nullable()->after('facebook_link');
            }

            if (! Schema::hasColumn('users', 'linkedin_link')) {
                $table->string('linkedin_link', 100)->nullable()->after('x_link');
            }

            if (! Schema::hasColumn('users', 'user_role_id')) {
                $table->unsignedInteger('user_role_id')->default(2)->index()->after('email_verified_at');
            }
        });

        if (Schema::hasColumn('users', 'name')) {
            DB::table('users')
                ->select(['id', 'name'])
                ->whereNotNull('name')
                ->orderBy('id')
                ->each(function (object $user): void {
                    $nameParts = preg_split('/\s+/', trim($user->name), 2);

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'firstname' => $nameParts[0] ?: 'Member',
                            'lastname' => $nameParts[1] ?? null,
                        ]);
                });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'firstname',
                'lastname',
                'nickname',
                'matno',
                'phone',
                'level_id',
                'member_type',
                'expectation_msg',
                'session_start',
                'session_end',
                'is_active',
                'is_ban',
                'fee_paid',
                'role',
                'bio',
                'dob',
                'image',
                'facebook_link',
                'x_link',
                'linkedin_link',
                'user_role_id',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
