<?php

namespace App\Observers;

use App\Models\Device;

class DeviceObserver
{
    public function creating(Device $device): void
    {
        $device->created_by = auth()->id();
        $device->updated_by = auth()->id();
    }

    public function updating(Device $device): void
    {
        $device->updated_by = auth()->id();
    }
}
