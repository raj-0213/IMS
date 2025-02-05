<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class Storedraft_productsRequest extends FormRequest
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
            'name' => 'required|string|unique:draft_products,name|max:255',
            'sales_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'manufacturer_name' => 'required|string|max:255',
            'is_banned' => 'boolean',
            'is_discontinued' => 'boolean',
            'is_assured' => 'boolean',
            'is_refrigerated' => 'boolean',
            'category_id' => 'required|integer|exists:categories,id',
            'product_status' => 'required|string|in:Draft,Published,Unpublished',
            'ws_code' => 'nullable|string|max:50',
            'combination' => 'nullable|string',
            'deleted_by' => 'nullable|integer|exists:users,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
