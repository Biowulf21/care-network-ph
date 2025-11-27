<?php

namespace App\Policies;

use App\Models\Clinic;
use App\Models\User;

class ClinicPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('superadmin') || $user->hasRole('admin');
    }

    public function view(User $user, Clinic $clinic): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasRole('admin') && $user->organization_id === $clinic->organization_id;
    }

    public function manage(User $user, Clinic $clinic): bool
    {
        return $user->hasRole('superadmin') || ($user->hasRole('admin') && $user->organization_id === $clinic->organization_id);
    }
}
