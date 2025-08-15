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
