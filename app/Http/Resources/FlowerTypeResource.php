<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlowerTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @OA\Schema(
     *     schema="FlowerType",
     *     type="object",
     *     title="FlowerType",
     *     required={"id", "name"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Hoa hồng"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-01T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-01T12:00:00Z")
     * )
     */

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
