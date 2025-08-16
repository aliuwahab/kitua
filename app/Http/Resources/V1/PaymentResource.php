<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'payment',
            'id' => $this->id,
            'attributes' => [
                'amount' => $this->amount,
                'formattedAmount' => $this->formatted_amount,
                'currencyCode' => $this->currency_code,
                'status' => $this->status,
                'paymentMethod' => $this->payment_method,
                'phoneNumber' => $this->phone_number,
                'accountNumber' => $this->account_number,
                'provider' => $this->provider,
                'providerReference' => $this->provider_reference,
                'providerPaymentMethod' => $this->provider_payment_method,
                'authorizationUrl' => $this->getAuthorizationUrl(),
                'accessCode' => $this->getAccessCode(),
                'isCompleted' => $this->is_completed,
                'isFailed' => $this->is_failed,
                'isPending' => $this->is_pending,
                'initiatedAt' => $this->initiated_at?->toISOString(),
                'completedAt' => $this->completed_at?->toISOString(),
                'failedAt' => $this->failed_at?->toISOString(),
                'failureReason' => $this->failure_reason,
                'failureMessage' => $this->failure_message,
                'metadata' => $this->metadata,
                'createdAt' => $this->created_at->toISOString(),
                'updatedAt' => $this->updated_at->toISOString(),
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user_id,
                    ],
                    'links' => [
                        'self' => route('users.show', $this->user_id),
                    ],
                ],
                $this->getPayableRelationshipName() => [
                    'data' => [
                        'type' => $this->getPayableRelationshipName(),
                        'id' => $this->payable_id,
                    ],
                    'links' => [
                        'self' => $this->getPayableResourceUrl(),
                    ],
                ],
            ],
            'links' => [
                'self' => '#', // Payment show route not implemented yet
            ],
        ];
    }

    /**
     * Get authorization URL from provider response.
     */
    private function getAuthorizationUrl(): ?string
    {
        return data_get($this->provider_response, 'data.authorization_url');
    }

    /**
     * Get access code from provider response.
     */
    private function getAccessCode(): ?string
    {
        return data_get($this->provider_response, 'data.access_code');
    }

    /**
     * Get the relationship name for the payable entity.
     */
    private function getPayableRelationshipName(): string
    {
        $className = class_basename($this->payable_type);
        return lcfirst($className);
    }

    /**
     * Get the resource URL for the payable entity.
     */
    private function getPayableResourceUrl(): string
    {
        return match ($this->payable_type) {
            'App\\Models\\PaymentRequest' => route('payment-requests.show', $this->payable_id),
            default => '#',
        };
    }
}
