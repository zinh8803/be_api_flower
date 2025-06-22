<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     @OA\Property(property="id", type="integer", example=9),
 *   @OA\Property(property="name", type="string", example="Hoa hồng"),
 *     @OA\Property(property="description", type="string", example="Nhập lô hoa hồng"),
 *    @OA\Property(property="status", type="boolean", example=1),
 *      @OA\Property(property="image_url", type="string", format="uri", example="https://example.com/image.jpg"),
 * 
 *     @OA\Property(
 *         property="details",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/RecipeDetailResource")
 *     )
 * ),
 * @OA\Schema(
 *     schema="RecipeInput",
 *     required={"flower_id", "quantity"},
 *     @OA\Property(property="flower_id", type="integer", example=1, description="ID hoa"),
 *     @OA\Property(property="quantity", type="integer", example=10, description="Số lượng hoa")
 * ),
 * @OA\Schema(
 *     schema="SizeInput",
 *     required={"size", "recipes"},
 *     @OA\Property(property="size", type="string", example="Nhỏ", description="Tên size sản phẩm"),
 *     @OA\Property(
 *         property="recipes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/RecipeInput"),
 *         description="Danh sách hoa theo công thức của size này"
 *     )
 * )
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
           // 'price' => $this->price,
            'status' => $this->status,
            'description' => $this->description,
            'image_url' => $this->image_url ,
            'category_id' => $this->category_id,
            //'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
          //  'receipt_details' => RecipeResource::collection($this->whenLoaded('recipes')),
            'sizes' => ProductSizeResource::collection($this->whenLoaded('productSizes')),
        ];
    }
}
