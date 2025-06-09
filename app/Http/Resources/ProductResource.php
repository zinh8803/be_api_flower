<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     @OA\Property(property="id", type="integer", example=9),
 *   @OA\Property(property="name", type="string", example="Hoa hồng"),
 *     @OA\Property(property="description", type="string", example="Nhập lô hoa hồng"),
 *     @OA\Property(property="price", type="number", format="double", example=900000),
 *    @OA\Property(property="status", type="boolean", example=1),
 *      @OA\Property(property="image", type="string", format="uri", example="https://example.com/image.jpg"),
 * 
 *     @OA\Property(
 *         property="details",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/RecipeDetailResource")
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
            'price' => $this->price,
            'status' => $this->status,
            'description' => $this->description,
            'image_url' => asset($this->image_url),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'receipt_details' => RecipeResource::collection($this->whenLoaded('recipes')),
        ];
    }
}
