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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');                    // Ton ID de commande dans ton système
            $table->string('identifier')->unique();        // ID unique envoyé à PayGate (ex: ORD_20251125_ABC123)
            $table->string('tx_reference')->nullable()->unique(); // Référence PayGateGlobal
            $table->string('payment_reference')->nullable();       // Référence Flooz/TMoney
            $table->decimal('amount', 15, 0);              // Montant demandé (FCFA)
            $table->decimal('amount_paid', 15, 0)->nullable(); // Montant réellement payé
            $table->string('phone_number');
            $table->enum('network', ['FLOOZ', 'TMONEY']);
            $table->string('payment_method')->nullable();  // FLOOZ ou TMONEY (retour webhook)

            // Statut du paiement
            $table->enum('status', ['pending', 'success', 'failed', 'expired', 'cancelled'])
                ->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->json('raw_response')->nullable(); // Pour debug : stocker la réponse brute du webhook si besoin

            $table->timestamps();

            // Index utiles
            $table->index('order_id');
            $table->index('tx_reference');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
