<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSession extends Model
{
    protected $fillable = [
        'session_id',
        'client_id',
        'amount',
        'token',
        'status'
    ];

    protected $casts = [
        // 'amount' => 'decimal:2'
    ];

    /**
     * Get the client that owns the payment session.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }


}
