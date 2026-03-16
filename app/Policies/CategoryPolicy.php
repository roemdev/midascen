<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function view(User $user, Category $category): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ["admin", "registrador"]);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->role === "admin";
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->role === "admin";
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === "admin";
    }
}
