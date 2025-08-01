<?php

namespace App\Http\Requests\FlowerType;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="FlowerTypeStoreRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Hoa ngày Tết")
 * )
 */
class StoreFlowerTypeRequest extends FormRequest
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
        ];
    }
}
