<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $timestamps = false;

    protected $table = 'events';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'nombre',
        'tipo_evento',
        'fecha_evento',
        'meta',
    ];

    protected $casts = [
        'fecha_evento' => 'date:Y-m-d',
    ];
}
