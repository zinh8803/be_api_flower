<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageNotification extends Notification
{
    use Queueable;
    protected $chatMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct($chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($chatMessage)
    {
        return [
            "id" => $this->chatMessage->id,
            "message" => $this->chatMessage->message,
        ];
    }

    public function toBroadcast($chatMessage)
    {
        return new BroadcastMessage([
            "id" => $this->chatMessage->id,
            "message" => $this->chatMessage->message,
        ]);
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }
}
