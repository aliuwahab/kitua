<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => $this->id,
            'attributes' => $this->filterFields([
                'mobileNumber' => $this->mobile_number,
                'firstName' => $this->first_name,
                'surname' => $this->surname,
                'otherNames' => $this->other_names,
                'fullName' => $this->full_name,
                'userType' => $this->user_type,
                'isActive' => $this->is_active,
                'emailVerifiedAt' => $this->email_verified_at?->toISOString(),
                'createdAt' => $this->created_at->toISOString(),
                'updatedAt' => $this->updated_at->toISOString(),
            ], 'user'),
            'relationships' => [
                'country' => [
                    'data' => $this->when($this->country_id, [
                        'type' => 'country',
                        'id' => $this->country_id
                    ]),
                    'links' => [
                        'self' => $this->when($this->country_id, 
                            route('countries.show', $this->country_id)
                        )
                    ]
                ],
                'paymentAccounts' => [
                    'data' => $this->paymentAccounts->map(fn($account) => [
                        'type' => 'paymentAccount',
                        'id' => $account->id
                    ]),
                    'links' => [
                        'related' => '#' // route('users.payment-accounts.index', $this->id)
                    ]
                ]
            ],
            'includes' => [
                'country' => new CountryResource($this->whenLoaded('country')),
                'paymentAccounts' => PaymentAccountResource::collection($this->whenLoaded('paymentAccounts'))
            ],
            'links' => [
                'self' => '#' // Temporarily disabled due to route issue
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
