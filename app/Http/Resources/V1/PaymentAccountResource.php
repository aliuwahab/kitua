<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'paymentAccount',
            'id' => $this->id,
            'attributes' => [
                'accountType' => $this->account_type,
                'accountNumber' => $this->account_number,
                'accountName' => $this->account_name,
                'provider' => $this->provider,
                'isPrimary' => $this->is_primary,
                'isVerified' => $this->is_verified,
                'isActive' => $this->is_active,
                'verifiedAt' => $this->verified_at?->toISOString(),
                'createdAt' => $this->created_at->toISOString(),
                'updatedAt' => $this->updated_at->toISOString(),
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'user',
                        'id' => $this->user_id
                    ],
                    'links' => [
                        'self' => '#' // Temporarily disabled due to route issue
                    ]
                ]
            ],
            'links' => [
                'self' => '#' // Temporarily disabled due to route issue
            ]
        ];
    }
}
