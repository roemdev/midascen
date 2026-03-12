<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Una marca tiene muchos modelos de equipo
    public function models()
    {
        return $this->hasMany(DeviceModel::class);
    }
}