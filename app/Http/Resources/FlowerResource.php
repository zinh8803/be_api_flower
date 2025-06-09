<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     /**
     * @OA\Schema(
     *     schema="Flower",
     *     type="object",
     *     title="Flower",
     *     required={"id", "name"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Hoa hồng"),
     *    @OA\Property(property="color", type="string", example="Đỏ"),
     *    @OA\Property(property="price", type="number", format="float", example=100.50),
     *    @OA\Property(property="flower_type", ref="#/components/schemas/FlowerType"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-01T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-01T12:00:00Z")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'price' => $this->price,
            'flower_type' => new FlowerTypeResource($this->whenLoaded('flowerType')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
