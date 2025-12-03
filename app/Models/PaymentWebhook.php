<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhook extends Model
{
    protected $fillable = ['order_id', 'idempotency_key', 'status', 'payload', 'processed'];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean'
    ];

}
