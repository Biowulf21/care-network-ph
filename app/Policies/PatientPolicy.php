<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function view(User $user, Patient $patient): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->organization_id === $patient->clinic->organization_id;
        }

        if ($user->hasRole('delegate')) {
            // delegates can view patients assigned to them - simplified to clinic scope
            return $user->clinic_id === $patient->clinic_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('superadmin') || $user->hasRole('admin');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $this->create($user) && ($user->hasRole('superadmin') || $user->organization_id === $patient->clinic->organization_id);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $this->update($user, $patient);
    }
}
