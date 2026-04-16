<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador", "observador"]);
    }

    public function view(User $user, Brand $brand): bool
    {
        return in_array($user->role, ["admin", "registrador", "observador"]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->role === "admin";
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->role === "admin";
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === "admin";
    }
}
