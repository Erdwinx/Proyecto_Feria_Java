<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseQueueRequest extends Model
{
    protected $table = 'purchase_queue_requests';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'payload',
        'status',
        'result',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
    ];
}
