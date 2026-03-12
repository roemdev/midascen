<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    protected $table = 'models'; // nombre real en la DB

    protected $fillable = [
        'category_id',
        'brand_id',
        'nombre',
        'descripcion',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'model_id');
    }
}