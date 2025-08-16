<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SettlePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by the controller policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'sometimes',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'payment_method' => [
                'required',
                'string',
                'in:card,bank_transfer,mobile_money,momo,ussd,qr_code'
            ],
            'phone_number' => [
                'required_if:payment_method,mobile_money,momo',
                'string',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/'
            ],
            'account_number' => [
                'nullable',
                'string',
                'max:20'
            ],
            'callback_url' => [
                'nullable',
                'url',
                'max:500'
            ],
            'metadata' => [
                'nullable',
                'array'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Payment amount must be at least 0.01.',
            'amount.max' => 'Payment amount cannot exceed 999,999.99.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
            'phone_number.required_if' => 'Phone number is required for mobile money payments.',
            'phone_number.regex' => 'Invalid phone number format.',
            'callback_url.url' => 'Callback URL must be a valid URL.',
        ];
    }

    /**
     * Get the payment data from the request.
     */
    public function getPaymentData(): array
    {
        $data = $this->only([
            'amount',
            'payment_method',
            'phone_number',
            'account_number',
            'callback_url'
        ]);

        // Add metadata if provided
        if ($this->has('metadata')) {
            $data['metadata'] = $this->input('metadata');
        }

        // Clean phone number
        if (!empty($data['phone_number'])) {
            $data['phone_number'] = $this->cleanPhoneNumber($data['phone_number']);
        }

        return array_filter($data); // Remove null/empty values
    }

    /**
     * Clean and format phone number.
     */
    private function cleanPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        
        // Ensure it starts with + if it doesn't already
        if (!str_starts_with($cleaned, '+')) {
            // Add country code based on length (basic logic)
            if (strlen($cleaned) === 10) {
                $cleaned = '+233' . $cleaned; // Ghana default
            } elseif (strlen($cleaned) === 11) {
                $cleaned = '+234' . substr($cleaned, 1); // Nigeria
            } else {
                $cleaned = '+' . $cleaned;
            }
        }
        
        return $cleaned;
    }

    /**
     * Get the body parameters for API documentation.
     * This method is used by Scribe to generate better API documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'payment_method' => [
                'description' => 'The payment method to use for the transaction.',
                'example' => 'mobile_money',
            ],
            'phone_number' => [
                'description' => 'The phone number for mobile money payments (required for mobile_money/momo).',
                'example' => '+233201234567',
            ],
            'amount' => [
                'description' => 'Custom amount for negotiable payment requests. If not provided, uses the original request amount.',
                'example' => 120.50,
            ],
            'account_number' => [
                'description' => 'Account number for bank transfers.',
                'example' => '1234567890',
            ],
            'callback_url' => [
                'description' => 'URL to redirect the user to after payment completion.',
                'example' => 'https://myapp.com/payment/callback',
            ],
            'metadata' => [
                'description' => 'Additional metadata for the payment.',
                'example' => ['note' => 'Payment for lunch', 'order_id' => 'ORD-123'],
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic can go here
            $paymentMethod = $this->input('payment_method');
            $phoneNumber = $this->input('phone_number');
            
            // Validate phone number format for mobile money
            if (in_array($paymentMethod, ['mobile_money', 'momo']) && $phoneNumber) {
                $cleanedPhone = $this->cleanPhoneNumber($phoneNumber);
                if (strlen($cleanedPhone) < 10 || strlen($cleanedPhone) > 15) {
                    $validator->errors()->add('phone_number', 'Phone number length is invalid.');
                }
            }
        });
    }
}
