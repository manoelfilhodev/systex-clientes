<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'subscription_id',
        'client_id',
        'invoice_number',
        'due_date',
        'amount',
        'payment_method',
        'status',
        'pix_txid',
        'pix_qr_code',
        'pix_copia_cola',
        'boleto_nosso_numero',
        'boleto_linha_digitavel',
        'boleto_barcode',
        'boleto_pdf_url',
        'inter_charge_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
