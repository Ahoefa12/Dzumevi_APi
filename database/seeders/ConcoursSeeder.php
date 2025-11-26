<?php

namespace Database\Seeders;

use App\Models\Concours;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConcoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Concours::factory()->count(5)->enCours()->create();
        Concours::factory()->count(3)->aVenir()->create();
        Concours::factory()->count(2)->passe()->create();
    }
}
