<?php

namespace App\Http\Requests\Size;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductSizeRequest extends FormRequest
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
        'product_id' => 'required|exists:products,id',
        'size' => 'required|string|max:50',
        'price' => 'required|numeric|min:0',
        ];
    }
}
