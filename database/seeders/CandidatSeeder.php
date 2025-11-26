<?php

namespace Database\Seeders;

use App\Models\Candidat;
use App\Models\Concours;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Créer des concours
        $concours = Concours::factory()
            ->count(3)
            ->enCours()
            ->create();

        // Créer des candidats pour chaque concours
        $concours->each(function ($concours) {
            Candidat::factory()
                ->count(8)
                ->create(['concours_id' => $concours->id]);
        });

        // Mettre à jour les stats des concours
        $concours->each(function ($concours) {
            $concours->updateStats();
        });
    }
}
