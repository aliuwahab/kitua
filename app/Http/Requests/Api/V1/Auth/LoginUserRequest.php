<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginUserRequest extends FormRequest
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
            'mobile_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'pin' => ['required', 'string', 'min:4', 'max:6', 'regex:/^[0-9]+$/'],
            
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
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile_number.regex' => 'The mobile number must be a valid phone number.',
            'pin.regex' => 'The PIN must contain only numbers.',
            'pin.min' => 'The PIN must be at least 4 digits.',
            'pin.max' => 'The PIN must not exceed 6 digits.',
            'device_type.in' => 'The device type must be either android or ios.',
        ];
    }

    /**
     * Get the credentials for authentication
     */
    public function getCredentials(): array
    {
        return $this->only(['mobile_number', 'pin']);
    }

    /**
     * Get the device data for login
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
     * Get the body parameters for API documentation.
     *
     * @return array<string, mixed>
     */
    public function bodyParameters(): array
    {
        return [
            'mobile_number' => [
                'description' => 'The user\'s mobile number (10-15 digits)',
                'example' => '0541234567',
            ],
            'pin' => [
                'description' => 'The user\'s 4-6 digit PIN',
                'example' => '1234',
            ],
            'device_id' => [
                'description' => 'Unique device identifier',
                'example' => 'device-12345-uuid',
            ],
            'device_name' => [
                'description' => 'Human readable device name',
                'example' => 'John\'s iPhone',
            ],
            'device_type' => [
                'description' => 'Type of device (android or ios)',
                'example' => 'ios',
            ],
            'app_version' => [
                'description' => 'Application version',
                'example' => '1.0.0',
            ],
            'os_version' => [
                'description' => 'Operating system version',
                'example' => '17.0',
            ],
            'device_model' => [
                'description' => 'Device model',
                'example' => 'iPhone 15 Pro',
            ],
            'screen_resolution' => [
                'description' => 'Screen resolution of the device',
                'example' => '1179x2556',
            ],
            'push_token' => [
                'description' => 'Device token for push notifications',
                'example' => 'apn-token-xyz123',
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
