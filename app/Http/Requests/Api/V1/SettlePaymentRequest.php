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
