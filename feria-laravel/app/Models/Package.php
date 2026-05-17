<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    public $timestamps = false;

    protected $table = 'packages';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nombre',
        'tipo_evento',
        'fecha_evento',
        'qr_text',
        'qr_generated_at',
    ];

    protected $casts = [
        'fecha_evento' => 'date:Y-m-d',
        'qr_generated_at' => 'datetime',
    ];

    /**
     * Relación: Un paquete tiene muchos boletos
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'package_id', 'id');
    }
}
