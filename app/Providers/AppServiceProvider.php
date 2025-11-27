<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicitly register Livewire dashboard components to avoid missing-component errors.
        if (class_exists(\App\Http\Livewire\Dashboard\Admin::class)) {
            Livewire::component('dashboard.admin', \App\Http\Livewire\Dashboard\Admin::class);
        }
        if (class_exists(\App\Http\Livewire\Dashboard\Superadmin::class)) {
            Livewire::component('dashboard.superadmin', \App\Http\Livewire\Dashboard\Superadmin::class);
        }
    }
}
