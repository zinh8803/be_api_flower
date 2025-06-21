<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'product_price' => $this->product ? $this->product->price : null,
            'product' => new ProductResource($this->whenLoaded('product')),
            'product_size' => new ProductSizeResource($this->whenLoaded('productSize')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
