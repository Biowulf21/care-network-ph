<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function view(User $user, MedicalRecord $record): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->organization_id === $record->clinic->organization_id;
        }

        if ($user->hasRole('delegate')) {
            // delegates can view records assigned to them
            return $record->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('delegate');
    }

    public function update(User $user, MedicalRecord $record): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->organization_id === $record->clinic->organization_id;
        }

        if ($user->hasRole('delegate')) {
            return $record->user_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, MedicalRecord $record): bool
    {
        return $this->update($user, $record);
    }
}
