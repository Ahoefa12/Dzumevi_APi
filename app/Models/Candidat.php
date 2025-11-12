<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidat extends Model
{
    protected $fillable = [
        "firstname",
        "matricule",
        "description",
        "categorie",
        "photo",
    ];

    
     public function candidat(): HasMany//un projet peu avoir plusieur tâches//
    {
        return $this->hasMany(Candidat::class);
    }
}
