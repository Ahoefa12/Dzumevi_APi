<?php

namespace Database\Factories;

use App\Models\Concours;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidat>
 */
class CandidatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'description' => $this->faker->paragraph(2),
            'categorie' => $this->faker->randomElement(['Homme', 'Femme', 'Autre']),
            'matricule' => 'MAT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'votes' => $this->faker->numberBetween(0, 1000),
            'photo' => $this->faker->optional()->imageUrl(200, 200, 'person'),
            'concours_id' => Concours::factory(),
        ];
    }
}
