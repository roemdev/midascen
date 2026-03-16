<?php

namespace App\Policies;

use App\Models\Recipient;
use App\Models\User;

class RecipientPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ["admin", "despachador"]);
    }

    public function view(User $user, Recipient $recipient): bool
    {
        return in_array($user->role, ["admin", "despachador"]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ["admin", "despachador"]);
    }

    public function update(User $user, Recipient $recipient): bool
    {
        return in_array($user->role, ["admin", "despachador"]);
    }

    public function delete(User $user, Recipient $recipient): bool
    {
        return $user->role === "admin";
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === "admin";
    }
}
