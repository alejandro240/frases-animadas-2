<?php

namespace App\Policies;

use App\Models\Frase;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FrasePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Frase $frase)
    {
        return $user->id === $frase->user_id;
    }

    public function update(User $user, Frase $frase)
    {
        return $user->id === $frase->user_id;
    }

    public function delete(User $user, Frase $frase)
    {
        return $user->id === $frase->user_id;
    }
}