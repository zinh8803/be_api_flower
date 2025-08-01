<?php

namespace App\Http\Requests\Flower;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="FlowerStoreRequest",
 *     required={"name"},
 *     required={"color_id", "status", "price", "flower_type_id"},
 *     @OA\Property(property="name", type="string", example="Hoa hồng đỏ"),
 *     @OA\Property(property="color_id", type="integer", example=1),
 *     @OA\Property(property="price", type="number", format="float", example=100000),
 *     @OA\Property(property="flower_type_id", type="integer", example=1),
 * )
 */
class StoreFlowerRequest extends FormRequest
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
            'color_id' => 'required|exists:colors,id',
            'price' => 'required|numeric|min:0',
            'flower_type_id' => 'required|exists:flower_types,id',
        ];
    }
}
