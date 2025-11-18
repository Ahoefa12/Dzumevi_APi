<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Candidat extends Model
{
    use HasFactory;

    protected $fillable = [
        "firstname",
        'matricule',
        "lastname",
        "description",
        "categorie",
        "photo",
        "vote_id",
    ];

    /**
     * Retourne l'URL publique complète de la photo si elle existe.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }

    /**
     * Supprimer la photo du stockage quand le candidat est supprimé.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($candidat) {
            if ($candidat->photo && Storage::disk('public')->exists($candidat->photo)) {
                Storage::disk('public')->delete($candidat->photo);
            }
        });
    }
}
