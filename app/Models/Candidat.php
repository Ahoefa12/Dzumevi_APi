<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidat extends Model
{
    protected $fillable = [
        "firstname",
        "lastname",
        "description",
        "categorie",
        "photo",
        "vote_id",
    ];

    
     public function candidat(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }
}
