<?php

namespace App\Http\Requests\Api\V1\PaymentRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePaymentRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'currency_code' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'purpose' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_negotiable' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a valid number.',
            'amount.min' => 'The amount must be at least 0.01.',
            'amount.max' => 'The amount cannot exceed 999,999,999.99.',
            'currency_code.required' => 'The currency code is required.',
            'currency_code.size' => 'The currency code must be exactly 3 characters.',
            'currency_code.regex' => 'The currency code must be 3 uppercase letters (e.g., USD, GHS).',
            'purpose.required' => 'The purpose is required.',
            'purpose.max' => 'The purpose cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'expires_at.after' => 'The expiration date must be in the future.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.max' => 'The image may not be greater than 5MB.',
        ];
    }

    /**
     * Get the body parameters for API documentation.
     *
     * @return array<string, mixed>
     */
    public function bodyParameters(): array
    {
        return [
            'amount' => [
                'description' => 'The amount to request for payment',
                'example' => 150.50,
            ],
            'currency_code' => [
                'description' => 'ISO 4217 currency code (3 letters)',
                'example' => 'GHS',
            ],
            'purpose' => [
                'description' => 'Brief description of what the payment is for',
                'example' => 'Lunch money',
            ],
            'description' => [
                'description' => 'Optional detailed description of the payment request',
                'example' => 'Need money for lunch at the office cafeteria today',
            ],
            'is_negotiable' => [
                'description' => 'Whether the payment amount is negotiable (default: false for fixed amount)',
                'example' => false,
            ],
            'expires_at' => [
                'description' => 'Optional expiration date for the payment request (ISO 8601 format)',
                'example' => '2025-08-20T18:00:00Z',
            ],
            'image' => [
                'description' => 'Optional image attachment (max 5MB, formats: jpeg, png, jpg, gif, webp)',
                'example' => 'image.jpg',
            ],
        ];
    }

    /**
     * Get the validated payment request data
     */
    public function getPaymentRequestData(): array
    {
        return $this->only([
            'amount',
            'currency_code',
            'purpose',
            'description',
            'is_negotiable',
            'expires_at'
        ]);
    }

    /**
     * Check if request has image attachment
     */
    public function hasImage(): bool
    {
        return $this->hasFile('image');
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422)
        );
    }
}
