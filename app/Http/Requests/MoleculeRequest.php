
<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;


class MoleculeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'molecule_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}