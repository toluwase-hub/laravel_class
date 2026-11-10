<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string|in:male,female,both',
            'price' => 'required|numeric|min:0|decimal:0,2',
            'searchProduct' => 'sometimes|string',
            'size' => 'required|array',
            'size.*.size' => 'required|integer|min:0|max:45',
            'size.*.in_stock' => 'required|integer|min:0',
        ];
    }
}
