<?php

namespace App\Models;

use App\Enums\VoteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'echeance',
        'statuts',
    ];

    /**
     * Cast the statuts attribute to the VoteStatus enum.
     *
     * This allows you to work with $vote->statuts as a VoteStatus instance
     * and to assign either a VoteStatus or a string value.
     */
    protected $casts = [
        'statuts' => VoteStatus::class,
        'date' => 'date',
        'echeance' => 'date',
    ];

    public function vote(): HasMany // un vote peut avoir plusieurs candidats
    {
        return $this->hasMany(Candidat::class);
    }
}
