<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceMovement extends Model
{
    protected $fillable = [
        'device_id',
        'user_id',
        'recipient_id',
        'tipo',
        'fecha_entrega',
        'fecha_devolucion',
        'motivo',
        'referencia',
    ];

    protected $casts = [
        'fecha_entrega'    => 'date',
        'fecha_devolucion' => 'date',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}