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
            $table->string('role', 32)->default('operator')->after('password');
        });
        DB::statement(
            "ALTER TABLE users
                    ADD CONSTRAINT users_role_check
                    CHECK(role IN('admin', 'manager', 'operator'))"
        );

        Schema::table('users', function (Blueprint $table): void {
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['role']);
        });

        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};
