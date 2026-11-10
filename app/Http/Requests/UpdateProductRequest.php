<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string|in:male,female,both',
            'price' => 'sometimes|numeric|min:0|decimal:0,2',
            // 'image' => 'sometimes|mimes:jpeg,png',
            'size' => 'sometimes|array',
            'size.*.size' => 'sometimes|integer|min:0|max:45',
            'size.*.in_stock' => 'sometimes|integer|min:0',
        ];
    }
}
