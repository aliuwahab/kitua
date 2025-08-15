<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterUserRequest extends FormRequest
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
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/', // International mobile number format
                // Note: We don't use unique here because existing users should be able to "register" again for login
            ],
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'surname' => ['required', 'string', 'min:2', 'max:50'],
            'other_names' => ['nullable', 'string', 'max:100'],
            'provider' => ['nullable', 'string', 'in:MTN,Vodafone,AirtelTigo,Glo'], // Mobile money providers
            
            // Device information
            'device_id' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
            'device_type' => ['required', 'string', 'in:android,ios'],
            'app_version' => ['nullable', 'string'],
            'os_version' => ['nullable', 'string'],
            'device_model' => ['nullable', 'string'],
            'screen_resolution' => ['nullable', 'string'],
            'push_token' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'mobile_number' => 'mobile number',
            'first_name' => 'first name',
            'device_id' => 'device ID',
            'device_type' => 'device type',
            'app_version' => 'app version',
            'os_version' => 'OS version',
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
            'mobile_number.regex' => 'The mobile number must be a valid phone number.',
            'mobile_number.unique' => 'This mobile number is already registered.',
            'provider.in' => 'The selected provider is invalid.',
            'device_type.in' => 'The device type must be either android or ios.',
        ];
    }

    /**
     * Get the user data for registration
     */
    public function getUserData(): array
    {
        return $this->only([
            'mobile_number',
            'first_name', 
            'surname',
            'other_names',
            'provider'
        ]);
    }

    /**
     * Get device-related data from request
     */
    public function getDeviceData(): array
    {
        return $this->only([
            'device_id',
            'device_name', 
            'device_type',
            'app_version',
            'os_version',
            'device_model',
            'screen_resolution',
            'push_token'
        ]);
    }

    /**
     * Get the body parameters for Scribe documentation
     */
    public function bodyParameters(): array
    {
        return [
            'mobile_number' => [
                'description' => 'User\'s mobile number in international format',
                'example' => '233244123456',
            ],
            'first_name' => [
                'description' => 'User\'s first name',
                'example' => 'John',
            ],
            'surname' => [
                'description' => 'User\'s surname/last name', 
                'example' => 'Doe',
            ],
            'other_names' => [
                'description' => 'User\'s other names (optional)',
                'example' => 'Michael',
            ],
            'provider' => [
                'description' => 'Mobile money provider (optional)',
                'example' => 'MTN',
            ],
            'device_id' => [
                'description' => 'Unique device identifier',
                'example' => 'device_12345',
            ],
            'device_name' => [
                'description' => 'User-friendly device name (optional)',
                'example' => 'John\'s iPhone',
            ],
            'device_type' => [
                'description' => 'Device type (android or ios)',
                'example' => 'android',
            ],
            'app_version' => [
                'description' => 'App version (optional)',
                'example' => '1.0.0',
            ],
            'os_version' => [
                'description' => 'Operating system version (optional)',
                'example' => 'Android 12',
            ],
            'device_model' => [
                'description' => 'Device model (optional)',
                'example' => 'Samsung Galaxy S21',
            ],
            'screen_resolution' => [
                'description' => 'Screen resolution (optional)',
                'example' => '1080x2340',
            ],
            'push_token' => [
                'description' => 'Firebase push token for notifications (optional)',
                'example' => 'firebase_token_123',
            ],
        ];
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
