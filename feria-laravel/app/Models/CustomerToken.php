<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerToken extends Model
{
    protected $table = 'customer_tokens';

    protected $fillable = [
        'customer_id',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
