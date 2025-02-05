<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublished_productsRequest extends FormRequest
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
            //      name' => 'required|string|max:255',
            //     'sales_price' => 'required|numeric|min:0',
            //     'mrp' => 'required|numeric|min:0',
            //     'manufacturer_name' => 'required|string|max:255',
            //     'is_banned' => 'required|boolean',
            //     'is_active' => 'required|boolean',
            //     'is_discontinued' => 'required|boolean',
            //     'is_assured' => 'required|boolean',
            //     'is_refridged' => 'required|boolean',
            //     'category_id' => 'required|integer|exists:categories,id', 
            //     'product_status' => 'nullable|string|in:Draft,Unpublished,Published',
            //     'combination' => 'nullable|string', 
        ];
    }
}
