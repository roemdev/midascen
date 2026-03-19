<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected $fillable = [
        'nombre',
        'departamento',
        'cargo',
        'supervisor',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(DeviceMovement::class);
    }

    // Equipos actualmente asignados
    public function activeDevices()
    {
        return $this->hasMany(DeviceMovement::class)
                    ->where('tipo', 'salida')
                    ->whereNull('fecha_devolucion');
    }

    public function categoryExceptions()
    {
        return $this->hasMany(RecipientCategoryException::class);
    }

    public function exceptionCategories()
    {
        return $this->belongsToMany(Category::class, 'recipient_category_exceptions')
                    ->select('categories.id', 'categories.nombre');
    }
}