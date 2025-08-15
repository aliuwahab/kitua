<?php

namespace App\Policies\V1;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;

class PaymentRequestPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($user->tokenCan(Abilities::ViewPaymentRequest)) {
            return true;
        } elseif ($user->tokenCan(Abilities::ViewOwnPaymentRequest)) {
            return $user->id === $paymentRequest->user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->tokenCan(Abilities::CreatePaymentRequest) ||
               $user->tokenCan(Abilities::CreateOwnPaymentRequest);
    }

    public function store(User $user): bool
    {
        return $this->create($user);
    }

    public function update(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($user->tokenCan(Abilities::UpdatePaymentRequest)) {
            return true;
        } elseif ($user->tokenCan(Abilities::UpdateOwnPaymentRequest)) {
            return $user->id === $paymentRequest->user_id;
        }

        return false;
    }

    public function replace(User $user, PaymentRequest $paymentRequest): bool
    {
        return $user->tokenCan(Abilities::ReplacePaymentRequest);
    }

    public function delete(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($user->tokenCan(Abilities::DeletePaymentRequest)) {
            return true;
        } elseif ($user->tokenCan(Abilities::DeleteOwnPaymentRequest)) {
            return $user->id === $paymentRequest->user_id;
        }

        return false;
    }

    public function destroy(User $user, PaymentRequest $paymentRequest): bool
    {
        return $this->delete($user, $paymentRequest);
    }

    public function viewAny(User $user): bool
    {
        return $user->tokenCan(Abilities::ViewPaymentRequest) ||
               $user->tokenCan(Abilities::ViewOwnPaymentRequest);
    }
}
