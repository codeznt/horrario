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
            $table->unsignedBigInteger('telegram_id')->unique()->nullable()->after('id');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->nullable()->after('last_name');
            $table->string('language_code', 10)->nullable()->after('username');
            $table->boolean('is_premium')->default(false)->after('language_code');
            $table->boolean('allows_write_to_pm')->default(false)->after('is_premium');
            $table->string('role')->default('user')->after('allows_write_to_pm');
            $table->timestamp('telegram_auth_date')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_id',
                'first_name',
                'last_name',
                'username',
                'language_code',
                'is_premium',
                'allows_write_to_pm',
                'role',
                'telegram_auth_date',
            ]);
        });
    }
};
