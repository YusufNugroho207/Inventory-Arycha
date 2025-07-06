<?php

namespace App\Policies;

use App\Models\User;

class LaporanStokBarangPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Manajer');
    }

    public function view(User $user): bool
    {
        return $user->hasRole('Manajer');
    }
}
