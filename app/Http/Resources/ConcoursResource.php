<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConcoursResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'date_debut' => $this->date_debut->toISOString(),
            'date_fin' => $this->date_fin->toISOString(),
            'statut' => $this->statut,
            'image_url' => $this->image_url,
            'prix_par_vote' => $this->prix_par_vote,
            'nombre_candidats' => $this->nombre_candidats,
            'nombre_votes' => $this->nombre_votes,
            'total_recettes' => $this->total_recettes,
            'is_active' => $this->is_active,
            'est_en_cours' => $this->est_en_cours,
            'est_a_venir' => $this->est_a_venir,
            'est_termine' => $this->est_termine,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
