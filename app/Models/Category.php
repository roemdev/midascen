<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Una categoría tiene muchos modelos de equipo
    public function models()
    {
        return $this->hasMany(DeviceModel::class);
    }
}