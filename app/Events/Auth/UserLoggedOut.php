<?php

namespace App\Events\Auth;

use App\Models\User;
use App\Models\DeviceSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedOut
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public DeviceSession $deviceSession,
        public string $reason = 'user_initiated'
    ) {}
}
