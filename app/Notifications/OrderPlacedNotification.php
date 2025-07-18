<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    protected $order;
    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }


    public function toDatabase($notifiable)
    {
        return [
            'order_code' => $this->order->order_code,
            'order_id' => $this->order->id,
            'customer_name' => $this->order->name,
            'total_price' => $this->order->total_price,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_code' => $this->order->order_code,
            'order_id' => $this->order->id,
            'customer_name' => $this->order->customer_name,
            'total_price' => $this->order->total_price,
        ]);
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }
}
