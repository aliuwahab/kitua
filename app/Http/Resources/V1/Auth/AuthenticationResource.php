<?php

namespace App\Http\Resources\V1\Auth;

use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthenticationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'authentication',
            'id' => $this->resource['user']->id,
            'attributes' => [
                'token' => $this->resource['token'],
                'isNewUser' => $this->resource['is_new_user'] ?? false,
                'isNewDevice' => $this->resource['is_new_device'] ?? false,
                'userExists' => $this->resource['user_exists'] ?? null,
                'mobileNumber' => $this->resource['mobile_number'] ?? $this->resource['user']->mobile_number,
                'message' => $this->resource['message'] ?? null,
                'pin' => $this->resource['pin'] ?? null,
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'user',
                        'id' => $this->resource['user']->id,
                    ],
                    'links' => [
                        'self' => '#' // Temporarily disabled due to route issue
                    ]
                ]
            ],
            'includes' => [
                'user' => new UserResource($this->resource['user'])
            ],
            'links' => [
                'self' => '#' // Temporarily disabled due to route issue
            ]
        ];
    }
}
