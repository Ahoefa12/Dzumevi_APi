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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidats')->cascadeOnDelete();
            $table->unsignedInteger('votes');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('country', 2);
            $table->string('status')->default('pending'); // pending, completed, failed, canceled
            $table->string('fedapay_transaction_id')->unique();
            $table->string('reference')->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
