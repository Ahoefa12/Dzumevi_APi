<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Concours>
 */
class ConcoursFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     public function definition(): array
    {
        $dateDebut = $this->faker->dateTimeBetween('now', '+1 month');
        $dateFin = $this->faker->dateTimeBetween($dateDebut, '+3 months');

        return [
            'name' => $this->faker->words(3, true) . ' Contest',
            'description' => $this->faker->paragraph(3),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'statut' => $this->faker->randomElement(['en cours', 'à venir', 'passé']),
            'image_url' => $this->faker->optional()->imageUrl(400, 300, 'contest'),
            'prix_par_vote' => 100,
            'nombre_candidats' => $this->faker->numberBetween(0, 50),
            'nombre_votes' => $this->faker->numberBetween(0, 1000),
            'total_recettes' => $this->faker->numberBetween(0, 100000),
            'is_active' => $this->faker->boolean(90), // 90% de chance d'être actif
        ];
    }

    public function enCours(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en cours',
            'date_debut' => now()->subDays(7),
            'date_fin' => now()->addDays(30),
        ]);
    }

    public function aVenir(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'à venir',
            'date_debut' => now()->addDays(15),
            'date_fin' => now()->addDays(45),
        ]);
    }

    public function passe(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'passé',
            'date_debut' => now()->subDays(60),
            'date_fin' => now()->subDays(30),
        ]);
    }
}
