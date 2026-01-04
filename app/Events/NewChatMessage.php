<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load(['sender', 'receiver']);
    }

    public function broadcastOn(): array
    {
        // Tin nhắn user -> nhóm admin/employee
        if ($this->message->to_staff_group) {
            return [
                new Channel('admin-messages'),
            ];
        }

        // Tin nhắn 1-1: admin/employee -> user hoặc user -> 1 staff cụ thể
        return [
            new PrivateChannel('chat.' . $this->message->receiver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewChatMessage';
    }

    public function broadcastWith(): array
    {
        return [
            'id'          => $this->message->id,
            'sender_id'   => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'group'       => $this->message->to_staff_group,
            'message'     => $this->message->message,
            'is_read'     => $this->message->is_read,
            'read_at'     => $this->message->read_at,
            'created_at'  => $this->message->created_at,
        ];
    }
}
