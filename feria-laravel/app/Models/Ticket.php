<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $timestamps = false;

    protected $table = 'tickets';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nombre',
        'fecha_evento',
        'escaneado',
        'customer_id',
    ];

    protected $casts = [
        'escaneado' => 'boolean',
        'fecha_evento' => 'date:Y-m-d',
    ];
}
