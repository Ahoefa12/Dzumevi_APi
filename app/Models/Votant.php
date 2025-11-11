<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Votant extends Model
{
    protected $fillable = [
        "email",
        "firstname",
        "email",
        "number",
    ];

     public function votant(): HasMany//un projet peu avoir plusieur tâches//
    {
        return $this->hasMany(Task::class);
    }
}
