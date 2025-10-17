<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'subscription_id',
        'client_id',
        'invoice_number',
        'due_date',
        'amount',
        'status',
    ];


    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class, 'client_id');
    }
}
