<?php

namespace App\Actions\PaymentRequest;

use App\Events\PaymentRequestUpdated;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdatePaymentRequest
{
    /**
     * Update a payment request
     */
    public function execute(
        PaymentRequest $paymentRequest, 
        User $user, 
        array $data, 
        ?UploadedFile $image = null,
        bool $removeImage = false
    ): PaymentRequest {
        return DB::transaction(function () use ($paymentRequest, $user, $data, $image, $removeImage) {
            // Store original data for event
            $originalData = $paymentRequest->only([
                'amount', 'currency_code', 'purpose', 'description', 'is_negotiable', 'expires_at', 'metadata'
            ]);

            // Extract image from data if provided
            $imageFromData = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imageFromData = $data['image'];
                unset($data['image']); // Remove from update data
            }

            // Update the payment request with filtered data
            $updateData = array_filter($data, fn($value) => $value !== null);
            $paymentRequest->update($updateData);

            // Handle image removal
            if ($removeImage) {
                $paymentRequest->clearMediaCollection('images');
            }

            // Handle new image attachment (prefer parameter over data)
            $imageToProcess = $image ?? $imageFromData;
            if ($imageToProcess) {
                // Clear existing images first (single file collection)
                $paymentRequest->clearMediaCollection('images');
                
                $paymentRequest->addMedia($imageToProcess)
                    ->toMediaCollection('images');
            }

            // Dispatch event for extensibility
            PaymentRequestUpdated::dispatch($paymentRequest, $user, $originalData);

            return $paymentRequest->fresh('media');
        });
    }
}
