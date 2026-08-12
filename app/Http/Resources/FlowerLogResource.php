<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FlowerLog",
 *     type="object",
 *     title="FlowerLog",
 *     required={"id", "flower_id", "type", "change_type", "quantity"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="flower_id", type="integer", example=2),
 *     @OA\Property(property="type", type="string", example="import", description="import, sale, cancel, adjustment"),
 *     @OA\Property(property="change_type", type="string", example="plus", description="plus hoặc minus"),
 *     @OA\Property(property="quantity", type="integer", example=50),
 *     @OA\Property(property="note", type="string", example="Nhập hoa theo phiếu HD001"),
 *     @OA\Property(property="reference_type", type="string", example="ImportReceipt"),
 *     @OA\Property(property="reference_id", type="integer", example=10),
 *     @OA\Property(property="flower", ref="#/components/schemas/Flower"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-08-12T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-08-12T10:00:00Z")
 * )
 */
class FlowerLogResource extends JsonResource
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
            'flower_id' => $this->flower_id,
            'type' => $this->type,
            'change_type' => $this->change_type,
            'quantity' => $this->quantity,
            'note' => $this->note,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'user_id' => $this->user_id,
            'flower' => new FlowerResource($this->whenLoaded('flower')),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
