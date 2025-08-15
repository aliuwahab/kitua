<?php

namespace App\Events\Auth;

use App\Models\User;
use App\Models\PaymentAccount;
use App\Models\DeviceSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public PaymentAccount $paymentAccount,
        public DeviceSession $deviceSession,
        public array $deviceInfo = []
    ) {}
}
