<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vote extends Model
{
    protected $fillable = [
       "name",
       "date",
       "echeance",
       "statuts",
    ];

     public function vote(): HasMany//un vote peut avoir plusieur //
    {
        return $this->hasMany(Candidat::class);
    }
}
