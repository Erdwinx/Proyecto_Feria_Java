<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = false;

    protected $table = 'customers';

    protected $fillable = [
        'nombre',
        'email',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Relación: Un cliente tiene muchos boletos
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'customer_id', 'id');
    }
}
