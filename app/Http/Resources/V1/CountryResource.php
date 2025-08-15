<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'country',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'code' => $this->code,
                'currencyCode' => $this->currency_code,
                'currencySymbol' => $this->currency_symbol,
                'currencyName' => $this->currency_name,
                'isActive' => $this->is_active,
                'createdAt' => $this->created_at->toISOString(),
                'updatedAt' => $this->updated_at->toISOString(),
            ],
            'links' => [
                'self' => route('countries.show', ['country' => $this->id])
            ]
        ];
    }
}
