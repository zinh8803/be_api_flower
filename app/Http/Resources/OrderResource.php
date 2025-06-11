<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="Order",
 *     @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Nguyen Van A"),
 *   @OA\Property(property="email", type="string", format="email", example="ngovinh@gmail.com"),
 *  @OA\Property(property="phone", type="string", example="0123456789"),
 *  @OA\Property(property="address", type="string", example="123 Nguyen Trai"),
 *  @OA\Property(property="note", type="string", example="Giao trong giờ hành chính", nullable=true),
 *     @OA\Property(property="total_price", type="number", example=250000),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class OrderResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'note' => $this->note,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'discount' => $this->discount ? [
                'name' => $this->discount->name,
                'type' => $this->discount->type,
                'value' => $this->discount->value,
                'amount_applied' => $this->discount_amount,
            ] : null,
            'buy_at' => $this->buy_at,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'order_details' => OrderDetailResource::collection($this->whenLoaded('orderDetails')),
        ];
    }
}
