<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'order_id',
        'identifier',
        'tx_reference',
        'payment_reference',
        'amount',
        'amount_paid',
        'phone_number',
        'network',
        'payment_method',
        'status',
        'paid_at',
        'raw_response',
    ];

    protected $casts = [
        'amount'       => 'decimal:0',
        'amount_paid'  => 'decimal:0',
        'paid_at'      => 'datetime',
        'raw_response' => 'array',
    ];

    // Scopes utiles
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Accessors
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'success';
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

