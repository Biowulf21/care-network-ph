<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Organization;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Policies\OrganizationPolicy;
use App\Policies\ClinicPolicy;
use App\Policies\PatientPolicy;
use App\Policies\MedicalRecordPolicy;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage-users', fn($user) => $user->hasRole('superadmin'));
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(Clinic::class, ClinicPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);
    }
}
