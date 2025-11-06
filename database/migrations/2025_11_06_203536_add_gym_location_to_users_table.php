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
            $table->foreignId('gym_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('primary_location_id')->nullable()->after('gym_id')->constrained('locations')->nullOnDelete();

            $table->index(['gym_id', 'status']);
        });

        // Create pivot table for users assigned to multiple locations
        Schema::create('location_user', function (Blueprint $table) {
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['location_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_user');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['gym_id']);
            $table->dropForeign(['primary_location_id']);
            $table->dropColumn(['gym_id', 'primary_location_id']);
        });
    }
};
