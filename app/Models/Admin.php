<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    protected $fillable = [
        "name",
        "password",
        "seConnecter",
        "creerConcours",
        "gereConcours",
        "creerCandidat",
        "gererCandidat",
        "suivreVotes",
        "suivreTransactions",
        "exporteResultat",
        "cloturerConcours",
    ];

    
     public function tasks(): HasMany//un projet peu avoir plusieur tâches//
    {
        return $this->hasMany(Task::class);
    }
}
