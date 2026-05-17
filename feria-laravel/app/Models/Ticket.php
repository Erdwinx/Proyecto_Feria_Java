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
        'package_id',
        'tipo_evento',
        'category',
        'seat_numbers',
    ];

    protected $casts = [
        'escaneado' => 'boolean',
        'fecha_evento' => 'date:Y-m-d',
        'seat_numbers' => 'array',
    ];

    /**
     * Relación: Un boleto pertenece a un paquete (opcional)
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    /**
     * Relación: Un boleto pertenece a un cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
