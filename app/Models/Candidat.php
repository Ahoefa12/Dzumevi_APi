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
        "maticule",
        "description",
        "categorie",
        "photo",
    ];

    
     public function tasks(): HasMany//un projet peu avoir plusieur tâches//
    {
        return $this->hasMany(Task::class);
    }
}
