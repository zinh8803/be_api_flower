<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductReportItem",
 *     type="object",
 *     required={"order_id", "order_detail_id", "quantity", "reason"},
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="order_detail_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="reason", type="string", example="Product damaged"),
 *     @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg"),
 * )
 */
class ProductReportResource extends JsonResource
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
            'order_id' => $this->order_id,
            'order_detail_id' => $this->order_detail_id,
            'user_id' => $this->user_id,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'admin_note' => $this->admin_note,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'action' => $this->action,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
