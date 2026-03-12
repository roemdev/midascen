<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'model_id',
        'numero_serie',
        'imei',
        'condicion',
        'disponibilidad',
        'notas',
    ];

    public function deviceModel()
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function movements()
    {
        return $this->hasMany(DeviceMovement::class);
    }

    // Movimiento activo actual (salida sin devolución)
    public function activeMovement()
    {
        return $this->hasOne(DeviceMovement::class)
                    ->where('tipo', 'salida')
                    ->whereNull('fecha_devolucion')
                    ->latest();
    }
}