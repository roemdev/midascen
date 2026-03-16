<?php

namespace App\Policies;

use App\Models\DeviceMovement;
use App\Models\User;

class DeviceMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador", "despachador"]);
    }

    public function view(User $user, DeviceMovement $movement): bool
    {
        return in_array($user->role, ["admin", "registrador", "despachador"]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ["admin", "despachador"]);
    }

    public function update(User $user, DeviceMovement $movement): bool
    {
        return $user->role === "admin";
    }

    public function delete(User $user, DeviceMovement $movement): bool
    {
        return $user->role === "admin";
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === "admin";
    }
}
