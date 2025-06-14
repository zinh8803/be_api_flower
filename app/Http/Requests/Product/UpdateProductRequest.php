<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="RecipeInputForUpdate",
 *     required={"flower_id", "quantity"},
 *     @OA\Property(property="flower_id", type="integer", example=1, description="ID hoa"),
 *     @OA\Property(property="quantity", type="integer", example=20, description="Số lượng hoa")
 * )
 *
 * @OA\Schema(
 *     schema="ProductUpdateRequest",
 *     required={"name", "recipes"},
 *     @OA\Property(property="name", type="string", example="Bó hoa cưới hồng đỏ"),
 *     @OA\Property(property="description", type="string", example="Bó hoa cưới được kết từ 10 bông hồng đỏ và 5 hoa baby"),
 *     @OA\Property(property="image", type="string", format="binary", description="Ảnh sản phẩm"),
 *     @OA\Property(property="status", type="integer", example=1, description="Trạng thái sản phẩm"),
 *     @OA\Property(property="size", type="string", example="Lớn"),
 *     @OA\Property(property="category_id", type="integer", example=1, description="ID danh mục hoa"),
 *     @OA\Property(
 *         property="recipes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/RecipeInputForUpdate")
 *     )
 * )
 */
class UpdateProductRequest extends FormRequest
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
            'description' => 'nullable|string|max:1000',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            //'price' => 'required|numeric|min:0',
            'status' => 'boolean',
            'size' => 'nullable|string|max:50',
            'category_id' => 'required|integer|exists:categories,id',
            'recipes' => 'required|array',
            'recipes.*.flower_id' => 'required|integer|exists:flowers,id',
            'recipes.*.quantity' => 'required|integer|min:1',
        ];
    }
    public function all($keys = null)
    {
        $data = parent::all($keys);
        if (isset($data['recipes']) && is_string($data['recipes'])) {
            $decoded = json_decode($data['recipes'], true);
            if (is_array($decoded)) {
                $data['recipes'] = $decoded;
            }
        }
        return $data;
    }
}
