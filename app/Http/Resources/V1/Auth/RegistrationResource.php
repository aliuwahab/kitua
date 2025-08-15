<?php

namespace App\Http\Resources\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'registration',
            'id' => uniqid(), // Generate a unique ID for this registration attempt
            'attributes' => [
                'userExists' => $this->resource['user_exists'] ?? false,
                'mobileNumber' => $this->resource['mobile_number'],
                'message' => $this->resource['message'],
                'pin' => $this->resource['pin'] ?? null, // Only for development/testing
            ],
            'links' => [
                'verifyPin' => route('api.v1.auth.verify-pin'),
                'login' => route('api.v1.auth.login'),
            ]
        ];
    }
}
