<?php

namespace App\Providers;

use App\Models\Device;
use App\Models\DeviceMovement;
use App\Observers\DeviceMovementObserver;
use App\Observers\DeviceObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Observers
        Device::observe(DeviceObserver::class);
        DeviceMovement::observe(DeviceMovementObserver::class);

        // Policies
        Gate::policy(
            \App\Models\Category::class,
            \App\Policies\CategoryPolicy::class,
        );
        Gate::policy(
            \App\Models\Brand::class,
            \App\Policies\BrandPolicy::class,
        );
        Gate::policy(
            \App\Models\DeviceModel::class,
            \App\Policies\DeviceModelPolicy::class,
        );
        Gate::policy(
            \App\Models\Device::class,
            \App\Policies\DevicePolicy::class,
        );
        Gate::policy(
            \App\Models\Recipient::class,
            \App\Policies\RecipientPolicy::class,
        );
        Gate::policy(
            \App\Models\DeviceMovement::class,
            \App\Policies\DeviceMovementPolicy::class,
        );
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
    }
}
