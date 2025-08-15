<?php

namespace App\Http\Resources\V1\Auth;

use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'logout',
            'id' => uniqid(), // Generate a unique ID for this logout action
            'attributes' => [
                'reason' => $this->resource['reason'] ?? 'user_initiated',
                'loggedOutAt' => $this->resource['logged_out_at'] ?? now()->toISOString(),
                'deviceSessionsCount' => $this->resource['device_sessions_count'] ?? null,
                'message' => $this->resource['message'] ?? 'Logged out successfully',
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'user',
                        'id' => $this->resource['user']->id,
                    ],
                    'links' => [
                        'self' => route('users.show', $this->resource['user']->id)
                    ]
                ]
            ],
            'includes' => [
                'user' => new UserResource($this->resource['user'])
            ]
        ];
    }
}
