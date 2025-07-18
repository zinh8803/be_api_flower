<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-orders'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'notification_id' => $this->notificationId ?? null,
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'name' => $this->order->name,
            'total_price' => $this->order->total_price,
            'status' => $this->order->status,
            'buy_at' => $this->order->buy_at,
            'payment_method' => $this->order->payment_method,
            'created_at' => $this->order->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->order->updated_at->format('Y-m-d H:i:s'),
            'user_id' => $this->order->user_id ?? null,
            'discount' => $this->order->discount ? [
                'id' => $this->order->discount->id,
                'name' => $this->order->discount->name,
                'type' => $this->order->discount->type,
                'value' => $this->order->discount->value,
                'status' => $this->order->discount->status,
                'start_date' => $this->order->discount->start_date,
                'end_date' => $this->order->discount->end_date,
                'min_total' => $this->order->discount->min_total,
            ] : null,
        ];
    }
}
