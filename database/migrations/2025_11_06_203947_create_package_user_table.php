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
        Schema::create('package_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_paid', 10, 2)->comment('Price at purchase time');
            $table->dateTime('purchased_at');
            $table->dateTime('starts_at')->nullable()->comment('When package becomes active');
            $table->dateTime('expires_at')->nullable()->comment('When package expires');
            $table->string('status')->default('active'); // active, expired, cancelled, suspended
            $table->string('payment_status')->default('pending'); // pending, paid, refunded, failed
            $table->string('payment_method')->nullable(); // credit_card, cash, bank_transfer, etc
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['package_id', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_user');
    }
};
