<?php

namespace App\Actions\PaymentRequest;

use App\Events\PaymentRequestDeleted;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeletePaymentRequest
{
    /**
     * Delete a payment request
     */
    public function execute(PaymentRequest $paymentRequest, User $user): bool
    {
        return DB::transaction(function () use ($paymentRequest, $user) {
            // Only allow deletion if the request is not paid
            if ($paymentRequest->status === 'paid') {
                throw new \Exception('Cannot delete a paid payment request.');
            }

            // Store data before deletion for event
            $paymentRequestData = $paymentRequest->toArray();

            // Clear any associated media first
            $paymentRequest->clearMediaCollection('images');

            // Delete the payment request
            $deleted = $paymentRequest->delete();

            if ($deleted) {
                // Create a model instance with the deleted data for the event
                $deletedPaymentRequest = new PaymentRequest($paymentRequestData);
                $deletedPaymentRequest->setAttribute('id', $paymentRequestData['id']);

                // Dispatch event for extensibility
                PaymentRequestDeleted::dispatch($deletedPaymentRequest, $user);
            }

            return $deleted;
        });
    }
}
