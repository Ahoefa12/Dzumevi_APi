<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidat extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'description',
        'categorie',
        'matricule',
        'votes',
        'photo',
        'vote_id',
    ];

    protected $casts = [
        'votes' => 'integer',
    ];

    /**
     * Relation avec le concours
     */
    public function concours(): BelongsTo
    {
        return $this->belongsTo(Concours::class);
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Incrémenter les votes
     */
    public function incrementVotes(int $votes = 1): void
    {
        $this->increment('votes', $votes);
        
        // Mettre à jour les stats du concours
        if ($this->concours) {
            $this->concours->updateStats();
        }
    }
    
     public function candidat(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }
}
