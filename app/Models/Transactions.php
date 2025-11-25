<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
        use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'candidate_id',
        'name',
        'email',
        'phone_number',
        'country',
        'reference',
        'currency',
        'amount',
        'votes',
        'status',
        'paid_at',
        'fedapay_transaction_id'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidat::class);
    }
}
