<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @OA\Schema(
     *     schema="Discount",
     *     type="object",
     *     title="Discount",
     *     required={"id", "name,type,value,start_date,end_date"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="HOA10"),
     *     @OA\Property(property="type", type="string", example="percent"),
     *     @OA\Property(property="start_date", type="string", format="date-time", example="2025-06-01T12:00:00Z"),
     *     @OA\Property(property="end_date", type="string", format="date-time", example="2025-06-01T12:00:00Z"),
     *     @OA\Property(property="status", type="boolean", example=true),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-01T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-01T12:00:00Z")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'type'       => $this->type,
            'value'      => $this->value,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'status'     => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
