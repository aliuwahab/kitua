<?php

namespace App\Events\Auth;

use App\Models\User;
use App\Models\DeviceSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public DeviceSession $deviceSession,
        public bool $isNewDevice = true
    ) {}
}
