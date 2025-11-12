<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidat extends Model
{
    protected $fillable = [
        "firstname",
        "lastname",
        "description",
        "categorie",
        "photo",
    ];

    
     public function candidat(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
