<?php

namespace App\Actions\PaymentRequest;

use App\Events\PaymentRequestCreated;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePaymentRequest
{
    /**
     * Create a new payment request
     */
    public function execute(User $user, array $data): PaymentRequest
    {
        return DB::transaction(function () use ($user, $data) {
            // Create the payment request (UUID will be auto-generated)
            $paymentRequest = $user->paymentRequests()->create([
                'amount' => $data['amount'],
                'currency_code' => $data['currency_code'],
                'purpose' => $data['purpose'],
                'description' => $data['description'] ?? null,
                'is_negotiable' => $data['is_negotiable'] ?? false,
                'expires_at' => $data['expires_at'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'status' => 'pending',
            ]);

            // Handle image attachment if provided
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $paymentRequest->addMedia($data['image'])
                    ->toMediaCollection('images');
            }

            // Dispatch event for extensibility
            PaymentRequestCreated::dispatch($paymentRequest, $user);

            return $paymentRequest->load('media');
        });
    }
}
