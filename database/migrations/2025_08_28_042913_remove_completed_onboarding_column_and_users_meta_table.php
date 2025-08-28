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
        // Drop the completed_onboarding column from users table since we use meta now
        if (Schema::hasColumn('users', 'completed_onboarding')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('completed_onboarding');
            });
        }

        // Drop the old users_meta table since we use the plank/metable 'meta' table now
        Schema::dropIfExists('users_meta');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the completed_onboarding column
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('completed_onboarding')->default(false);
        });

        // Recreate the users_meta table
        Schema::create('users_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }
};
