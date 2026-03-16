<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        "model_id",
        "numero_serie",
        "imei",
        "condicion",
        "disponibilidad",
        "notas",
        "created_by",
        "updated_by",
    ];

    public function deviceModel()
    {
        return $this->belongsTo(DeviceModel::class, "model_id");
    }

    public function movements()
    {
        return $this->hasMany(DeviceMovement::class);
    }

    public function activeMovement()
    {
        return $this->hasOne(DeviceMovement::class)
            ->where("tipo", "salida")
            ->whereNull("fecha_devolucion")
            ->latest();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }
}
