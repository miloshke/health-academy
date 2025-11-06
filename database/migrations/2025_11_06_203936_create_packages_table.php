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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_days')->comment('Package validity in days');
            $table->json('benefits')->nullable()->comment('JSON array of benefits/features');
            $table->integer('group_access_limit')->nullable()->comment('Max groups user can enroll, null = unlimited');
            $table->boolean('unlimited_access')->default(false)->comment('Full gym access');
            $table->string('status')->default('active'); // active, inactive, archived
            $table->timestamps();

            $table->index(['gym_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
