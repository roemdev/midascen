<?php

namespace App\Policies;

use App\Models\DeviceModel;
use App\Models\User;

class DeviceModelPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function view(User $user, DeviceModel $deviceModel): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function update(User $user, DeviceModel $deviceModel): bool
    {
        return $user->role === "admin";
    }

    public function delete(User $user, DeviceModel $deviceModel): bool
    {
        return $user->role === "admin";
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === "admin";
    }
}
