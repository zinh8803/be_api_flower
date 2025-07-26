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
     *    @OA\Property(property="min_total", type="float", example=100000.0),
     *    @OA\Property(property="usage_limit", type="integer", example=1),
     *    @OA\Property(property="usage_count", type="integer", example=0),
     *     @OA\Property(property="status", type="boolean", example=true),
     *    @OA\Property(property="value", type="float", example=10.0),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-01T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-01T12:00:00Z")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'name'       => $this->name,
            'type'       => $this->type,
            'value'      => $this->value,
            'min_total'  => $this->min_total,
            'usage_limit' => $this->usage_limit,
            'usage_count' => $this->usage_count,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'status'     => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
