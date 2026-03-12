<?php

namespace App\Providers;

use App\Models\DeviceMovement;
use App\Observers\DeviceMovementObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        DeviceMovement::observe(DeviceMovementObserver::class);
    }
}