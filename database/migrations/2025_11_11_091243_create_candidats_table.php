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
        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname')->nullable();
            $table->string('name')->before('name')->nullable();
            $table->integer('votes')->nullable();

            $table->string('matricule')->unique();
            $table->string('description', 355)->nullable();
            $table->enum('categorie', ['Homme', 'Femme', 'Autre']);
            $table->string('photo')->nullable();
            // $table->foreignId('vote_id')
            //     ->nullable()
            //     ->on('votes')
            //     ->references('id')
            //     ->onDelete('cascade');
            // $table->foreignId('concours_id')
            //     ->nullable()
            //     ->references('id')
            //     ->on('concours')
            //     ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidats');
    }
};
