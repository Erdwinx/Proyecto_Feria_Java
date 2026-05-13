<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanLogEntry extends Model
{
    public $timestamps = false;

    protected $table = 'scan_log';

    protected $fillable = [
        'ticket_id',
        'nombre',
        'scanned_at_epoch_seconds',
    ];
}
