<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

abstract class BasePaymentRequestRequest extends FormRequest
{
    /**
     * Map JSON:API structured request to model attributes
     */
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge([
            'data.attributes.amount' => 'amount',
            'data.attributes.currencyCode' => 'currency_code',
            'data.attributes.purpose' => 'purpose',
            'data.attributes.description' => 'description',
            'data.attributes.isNegotiable' => 'is_negotiable',
            'data.attributes.status' => 'status',
            'data.attributes.expiresAt' => 'expires_at',
            'data.attributes.paidAt' => 'paid_at',
            'data.attributes.metadata' => 'metadata',
            'data.relationships.author.data.id' => 'user_id',
        ], $otherAttributes);

        $attributesToUpdate = [];
        
        // Handle both JSON:API and legacy formats
        if ($this->has('data')) {
            // JSON:API format
            foreach ($attributeMap as $key => $attribute) {
                if ($this->has($key)) {
                    $attributesToUpdate[$attribute] = $this->input($key);
                }
            }
        } else {
            // Legacy format (for backward compatibility)
            $legacyMap = [
                'amount' => 'amount',
                'currency_code' => 'currency_code',
                'purpose' => 'purpose',
                'description' => 'description',
                'is_negotiable' => 'is_negotiable',
                'status' => 'status',
                'expires_at' => 'expires_at',
                'paid_at' => 'paid_at',
                'metadata' => 'metadata',
                'user_id' => 'user_id',
            ];

            foreach ($legacyMap as $key => $attribute) {
                if ($this->has($key)) {
                    $attributesToUpdate[$attribute] = $this->input($key);
                }
            }
        }

        return $attributesToUpdate;
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'data.attributes.amount.required' => 'The amount is required.',
            'data.attributes.amount.numeric' => 'The amount must be a number.',
            'data.attributes.amount.min' => 'The amount must be at least :min.',
            'data.attributes.currencyCode.required' => 'The currency code is required.',
            'data.attributes.currencyCode.string' => 'The currency code must be a string.',
            'data.attributes.currencyCode.size' => 'The currency code must be exactly :size characters.',
            'data.attributes.purpose.required' => 'The purpose is required.',
            'data.attributes.purpose.string' => 'The purpose must be a string.',
            'data.attributes.purpose.max' => 'The purpose may not be greater than :max characters.',
            'data.attributes.description.string' => 'The description must be a string.',
            'data.attributes.isNegotiable.boolean' => 'The negotiable field must be true or false.',
            'data.attributes.status.in' => 'The status must be one of: pending, paid, cancelled, expired.',
            'data.attributes.expiresAt.date' => 'The expiry date must be a valid date.',
            'data.attributes.expiresAt.after' => 'The expiry date must be after now.',
            'data.attributes.metadata.array' => 'The metadata must be an array.',
            
            // Legacy format messages
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least :min.',
            'currency_code.required' => 'The currency code is required.',
            'currency_code.string' => 'The currency code must be a string.',
            'currency_code.size' => 'The currency code must be exactly :size characters.',
            'purpose.required' => 'The purpose is required.',
            'purpose.string' => 'The purpose must be a string.',
            'purpose.max' => 'The purpose may not be greater than :max characters.',
            'description.string' => 'The description must be a string.',
            'is_negotiable.boolean' => 'The negotiable field must be true or false.',
            'status.in' => 'The status must be one of: pending, paid, cancelled, expired.',
            'expires_at.date' => 'The expiry date must be a valid date.',
            'expires_at.after' => 'The expiry date must be after now.',
            'metadata.array' => 'The metadata must be an array.',
        ];
    }

    /**
     * Check if format is JSON:API
     */
    protected function isJsonApiFormat(): bool
    {
        return $this->has('data');
    }

    /**
     * Get the base validation rules (can be overridden)
     */
    protected function getBaseRules(): array
    {
        if ($this->isJsonApiFormat()) {
            return [
                'data' => 'required|array',
                'data.type' => 'required|string|in:paymentRequest',
                'data.attributes' => 'required|array',
                'data.attributes.amount' => 'required|numeric|min:0.01',
                'data.attributes.currencyCode' => 'required|string|size:3',
                'data.attributes.purpose' => 'required|string|max:255',
                'data.attributes.description' => 'nullable|string|max:1000',
                'data.attributes.isNegotiable' => 'sometimes|boolean',
                'data.attributes.status' => 'sometimes|string|in:pending,paid,cancelled,expired',
                'data.attributes.expiresAt' => 'nullable|date|after:now',
                'data.attributes.metadata' => 'sometimes|array',
            ];
        } else {
            // Legacy format rules
            return [
                'amount' => 'required|numeric|min:0.01',
                'currency_code' => 'required|string|size:3',
                'purpose' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_negotiable' => 'sometimes|boolean',
                'status' => 'sometimes|string|in:pending,paid,cancelled,expired',
                'expires_at' => 'nullable|date|after:now',
                'metadata' => 'sometimes|array',
            ];
        }
    }
}
