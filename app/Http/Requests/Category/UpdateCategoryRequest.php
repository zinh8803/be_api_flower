<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="CategoryUpdateRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Hoa tặng mẹ"),
 *     @OA\Property(property="image", type="string", format="binary", description="Upload file ảnh"),
 *     
 * )
 */
class UpdateCategoryRequest extends FormRequest
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
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
