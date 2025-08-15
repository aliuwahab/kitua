<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'paymentRequest',
            'id' => $this->id,
            'attributes' => $this->filterFields([
                'amount' => $this->amount,
                'formattedAmount' => $this->formatted_amount,
                'currencyCode' => $this->currency_code,
                'purpose' => $this->purpose,
                'description' => $this->when(
                    !$request->routeIs(['payment-requests.index']),
                    $this->description
                ),
                'isNegotiable' => $this->is_negotiable,
                'status' => $this->status,
                'expiresAt' => $this->expires_at?->toISOString(),
                'paidAt' => $this->paid_at?->toISOString(),
                'isExpired' => $this->is_expired,
                'metadata' => $this->when(
                    !$request->routeIs(['payment-requests.index']),
                    $this->metadata
                ),
                'createdAt' => $this->created_at?->toISOString(),
                'updatedAt' => $this->updated_at?->toISOString(),
            ], 'paymentRequest'),
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user_id
                    ],
                    'links' => [
                        'self' => $this->user_id ? route('users.show', $this->user_id) : null
                    ]
                ]
            ],
            'includes' => new UserResource($this->whenLoaded('user')),
            'links' => [
                'self' => $this->id ? route('payment-requests.show', $this->id) : null
            ]
        ];
    }

    /**
     * Filter fields based on sparse fieldsets
     */
    private function filterFields(array $attributes, string $resourceType): array
    {
        $fields = request()->get('fields');
        
        if (!$fields || !isset($fields[$resourceType])) {
            return $attributes;
        }

        $allowedFields = explode(',', $fields[$resourceType]);
        
        return array_filter($attributes, function($key) use ($allowedFields) {
            return in_array($key, $allowedFields);
        }, ARRAY_FILTER_USE_KEY);
    }
}
