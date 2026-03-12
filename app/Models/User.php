<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'activo'   => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Relación: un usuario registra muchos movimientos
    public function movements()
    {
        return $this->hasMany(DeviceMovement::class);
    }
}