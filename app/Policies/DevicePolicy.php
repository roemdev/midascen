<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

class DevicePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador", "despachador", "observador"]);
    }

    public function view(User $user, Device $device): bool
    {
        return in_array($user->role, ["admin", "registrador", "despachador", "observador"]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function update(User $user, Device $device): bool
    {
        if ($user->role === "admin") {
            return true;
        }
        if ($user->role === "registrador") {
            return $device->created_by === $user->id;
        }
        return false;
    }

    public function delete(User $user, Device $device): bool
    {
        return $user->role === "admin";
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === "admin";
    }
}
