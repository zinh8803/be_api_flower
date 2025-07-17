<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @OA\Schema(
     *     schema="Category",
     *     type="object",
     *     title="FlowerType",
     *     required={"id", "name"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Hoa tặng mẹ"),
     *    @OA\Property(property="slug", type="string", example="hoa-tang-me"),
     *     @OA\Property(property="image_url", type="string", format="uri", example="https://example.com/image.jpg"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-01T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-01T12:00:00Z")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
