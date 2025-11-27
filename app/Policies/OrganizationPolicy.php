<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('superadmin');
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->hasRole('superadmin') || $user->belongsToOrganization($organization->id);
    }

    public function manage(User $user, Organization $organization): bool
    {
        return $user->hasRole('superadmin');
    }
}
