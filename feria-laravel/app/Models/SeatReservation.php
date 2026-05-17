<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatReservation extends Model
{
    protected $table = 'seat_reservations';
    protected $fillable = ['fecha_evento', 'category', 'seat_number', 'ticket_id', 'status'];
    protected $casts = ['fecha_evento' => 'date:Y-m-d'];
}
