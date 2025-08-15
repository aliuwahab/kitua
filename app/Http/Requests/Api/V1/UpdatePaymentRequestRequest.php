<?php

namespace App\Http\Requests\Api\V1;

class UpdatePaymentRequestRequest extends BasePaymentRequestRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by policies
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $baseRules = $this->getBaseRules();
        
        // Make all fields optional for updates (PATCH)
        if ($this->isJsonApiFormat()) {
            $baseRules['data.attributes.amount'] = 'sometimes|numeric|min:0.01';
            $baseRules['data.attributes.currencyCode'] = 'sometimes|string|size:3';
            $baseRules['data.attributes.purpose'] = 'sometimes|string|max:255';
            $baseRules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $baseRules['remove_image'] = 'sometimes|boolean';
        } else {
            $baseRules['amount'] = 'sometimes|numeric|min:0.01';
            $baseRules['currency_code'] = 'sometimes|string|size:3';
            $baseRules['purpose'] = 'sometimes|string|max:255';
            $baseRules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $baseRules['remove_image'] = 'sometimes|boolean';
        }

        return $baseRules;
    }

    /**
     * Get Scribe body parameters for documentation
     */
    public function bodyParameters()
    {
        return [
            'amount' => [
                'description' => 'The amount of the payment request',
                'example' => 200,
            ],
            'purpose' => [
                'description' => 'The purpose of the payment request',
                'example' => 'Updated lunch payment',
            ],
            'description' => [
                'description' => 'A longer description of the payment request',
                'example' => 'Updated payment for team lunch',
            ],
            'expires_at' => [
                'description' => 'The date and time when the payment request expires',
                'example' => '2025-10-15T12:00:00Z',
            ],
            'metadata' => [
                'description' => 'Additional metadata for the payment request as JSON object',
                'example' => '{"restaurant":"Updated Cafe","receipt_number":"RCT-67890"}',
            ],
            'negotiable' => [
                'description' => 'Whether the amount is negotiable',
                'example' => true,
            ],
            'image' => [
                'description' => 'A new image to attach to the payment request (jpg, png, gif, webp). Maximum size: 2MB.',
            ],
            'remove_image' => [
                'description' => 'Whether to remove the existing image',
                'example' => true,
            ],
        ];
    }

    /**
     * Get payment request data for updating
     */
    public function getPaymentRequestData(): array
    {
        return $this->mappedAttributes();
    }

    /**
     * Check if image is provided
     */
    public function hasImage(): bool
    {
        return $this->hasFile('image');
    }

    /**
     * Check if image should be removed
     */
    public function shouldRemoveImage(): bool
    {
        return $this->boolean('remove_image');
    }
}
