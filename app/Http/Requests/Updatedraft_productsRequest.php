<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Updatedraft_productsRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'sales_price' => 'sometimes|required|numeric',
            'mrp' => 'sometimes|required|numeric',
            'manufacturer_name' => 'nullable|string|max:255',
            'is_banned' => 'sometimes|required|boolean',
            'is_discontinued' => 'sometimes|required|boolean',
            'is_assured' => 'sometimes|required|boolean',
            'is_refridged' => 'sometimes|required|boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
            'product_status' => 'sometimes|required|in:Draft,Published,Unpublished',
            'ws_code' => 'nullable|integer',
            'combination' => 'nullable|array',
            'combination.*' => 'string',
            'published_by' => 'nullable|exists:users,id',
            'published_at' => 'nullable|date'
        ];
    }
}
