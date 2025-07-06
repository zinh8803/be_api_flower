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
            'category_id' => 'required|integer|exists:categories,id',
            'sizes' => 'required|array|min:1',
        'sizes.*.size' => 'required|string|max:255',
        'sizes.*.price' => 'required|numeric|min:0',
        'sizes.*.recipes' => 'required|array|min:1',
        'sizes.*.recipes.*.flower_id' => 'required|integer|exists:flowers,id',
        'sizes.*.recipes.*.quantity' => 'required|integer|min:1',
        ];
    }
    public function all($keys = null)
    {
        $data = parent::all($keys);
        if (isset($data['sizes']) && is_string($data['sizes'])) {
            $decoded = json_decode($data['sizes'], true);
            if (is_array($decoded)) {
                $data['sizes'] = $decoded;
            }
        }
        return $data;
    }
}
