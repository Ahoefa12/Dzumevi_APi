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
        Schema::create('concours', function (Blueprint $table) {
             $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->enum('statut', ['en cours', 'à venir', 'passé'])->default('à venir');
            $table->string('image_url')->nullable();
            $table->integer('prix_par_vote')->default(100); // 100 FCFA par défaut
            $table->integer('nombre_candidats')->default(0);
            $table->integer('nombre_votes')->default(0);
            $table->integer('total_recettes')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index pour les performances
            $table->index(['statut', 'is_active']);
            $table->index('date_debut');
            $table->index('date_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concours');
    }
};
