<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concours extends Model
{
    /** @use HasFactory<\Database\Factories\ConcoursFactory> */
    use HasFactory;


    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'date_debut',
        'date_fin',
        'statut',
        'image_url',
        'prix_par_vote',
        'nombre_candidats',
        'nombre_votes',
        'total_recettes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'is_active' => 'boolean',
        'prix_par_vote' => 'integer',
        'nombre_candidats' => 'integer',
        'nombre_votes' => 'integer',
        'total_recettes' => 'integer',
    ];

    /**
     * Scope pour les concours actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'en cours')
                    ->where('is_active', true);
    }

    /**
     * Scope pour les concours à venir
     */
    public function scopeAVenir($query)
    {
        return $query->where('statut', 'à venir')
                    ->where('is_active', true);
    }

    /**
     * Scope pour les concours passés
     */
    public function scopePasses($query)
    {
        return $query->where('statut', 'passé')
                    ->where('is_active', true);
    }

    /**
     * Relation avec les candidats
     */
    public function candidats(): HasMany
    {
        return $this->hasMany(Candidat::class);
    }

    /**
     * Accessor pour le statut formaté
     */
    public function getStatutFormattedAttribute(): string
    {
        return match($this->statut) {
            'en cours' => 'En Cours',
            'à venir' => 'À Venir',
            'passé' => 'Terminé',
            default => $this->statut
        };
    }

    /**
     * Accessor pour vérifier si le concours est en cours
     */
    public function getEstEnCoursAttribute(): bool
    {
        return $this->statut === 'en cours';
    }

    /**
     * Accessor pour vérifier si le concours est à venir
     */
    public function getEstAVenirAttribute(): bool
    {
        return $this->statut === 'à venir';
    }

    /**
     * Accessor pour vérifier si le concours est terminé
     */
    public function getEstTermineAttribute(): bool
    {
        return $this->statut === 'passé';
    }

    /**
     * Méthode pour mettre à jour les statistiques
     */
    public function updateStats(): void
    {
        $this->update([
            'nombre_candidats' => $this->candidats()->count(),
            'nombre_votes' => $this->candidats()->sum('votes'),
            'total_recettes' => $this->candidats()->sum('votes') * $this->prix_par_vote,
        ]);
    }

    /**
     * Méthode pour activer/désactiver le concours
     */
    public function toggleStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }
}

