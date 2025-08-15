<?php

namespace App\Http\Requests\Api\V1;

class StorePaymentRequestRequest extends BasePaymentRequestRequest
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
        
        // Add image validation for creation
        if ($this->isJsonApiFormat()) {
            $baseRules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        } else {
            $baseRules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
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
                'example' => 150,
            ],
            'purpose' => [
                'description' => 'The purpose of the payment request (max 100 characters)',
                'example' => 'Lunch payment',
            ],
            'description' => [
                'description' => 'A longer description of the payment request',
                'example' => 'Payment for team lunch at the cafeteria',
            ],
            'expires_at' => [
                'description' => 'The date and time when the payment request expires. If not provided, defaults to 30 days from creation.',
                'example' => '2025-09-15T12:00:00Z',
            ],
            'metadata' => [
                'description' => 'Additional metadata for the payment request as JSON object',
                'example' => '{"restaurant":"Cafe Royal","receipt_number":"RCT-12345"}',
            ],
            'negotiable' => [
                'description' => 'Whether the amount is negotiable. Default is false.',
                'example' => false,
            ],
            'image' => [
                'description' => 'An image to attach to the payment request (jpg, png, gif, webp). Maximum size: 2MB.',
            ],
        ];
    }

    /**
     * Get payment request data for creating
     */
    public function getPaymentRequestData(): array
    {
        $data = $this->mappedAttributes();
        
        // Set user_id to authenticated user if not provided
        if (!isset($data['user_id'])) {
            $data['user_id'] = $this->user()->id;
        }
        
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        // Set default currency if not provided
        if (!isset($data['currency_code'])) {
            $data['currency_code'] = 'GHS'; // Default to Ghana Cedis
        }

        return $data;
    }

    /**
     * Check if image is provided
     */
    public function hasImage(): bool
    {
        return $this->hasFile('image');
    }
}
