<?php

namespace App\Http\Requests\Api\V1;

class ReplacePaymentRequestRequest extends BasePaymentRequestRequest
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
        
        // For PUT requests, all required fields must be present
        $baseRules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        
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
                'example' => 250,
            ],
            'purpose' => [
                'description' => 'The purpose of the payment request',
                'example' => 'Replaced lunch payment',
            ],
            'description' => [
                'description' => 'A longer description of the payment request',
                'example' => 'Completely replaced payment for team lunch',
            ],
            'expires_at' => [
                'description' => 'The date and time when the payment request expires',
                'example' => '2025-11-15T12:00:00Z',
            ],
            'negotiable' => [
                'description' => 'Whether the amount is negotiable',
                'example' => false,
            ],
        ];
    }

    /**
     * Get payment request data for replacing
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
