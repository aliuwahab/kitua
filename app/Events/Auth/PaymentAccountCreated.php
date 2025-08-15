<?php

namespace App\Events\Auth;

use App\Models\User;
use App\Models\PaymentAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentAccountCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public PaymentAccount $paymentAccount,
        public bool $isPrimary = false
    ) {}
}
