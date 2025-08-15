<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyPinRequest extends FormRequest
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
            'mobile_number' => ['required', 'string', 'exists:users,mobile_number'],
            'pin' => ['required', 'string', 'min:4', 'max:6', 'regex:/^[0-9]+$/'],
            
            // Device information for device session creation
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
            'mobile_number.exists' => 'No registration found for this mobile number.',
            'pin.regex' => 'The PIN must contain only numbers.',
            'pin.min' => 'The PIN must be at least 4 digits.',
            'pin.max' => 'The PIN must not exceed 6 digits.',
            'device_type.in' => 'The device type must be either android or ios.',
        ];
    }

    /**
     * Get the device data for verification
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
