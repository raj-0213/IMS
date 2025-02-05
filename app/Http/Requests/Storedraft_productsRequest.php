<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string|max:255',
            'sales_price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'manufacturer_name' => 'nullable|string|max:255',
            'is_banned' => 'required|boolean',
            'is_discontinued' => 'required|boolean',
            'is_assured' => 'required|boolean',
            'is_refridged' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'product_status' => 'required|in:Draft,Published,Unpublished',
            'ws_code' => 'nullable|integer',
            'combination' => 'nullable|array',
            'combination.*' => 'string',
            'published_by' => 'nullable|exists:users,id',
            'published_at' => 'nullable|date'
        ];
    }
}
