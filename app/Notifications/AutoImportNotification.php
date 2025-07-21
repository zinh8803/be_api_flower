<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AutoImportNotification extends Notification
{
    protected $AutoImport;

    /**
     * Create a new notification instance.
     */
    public function __construct($AutoImport)
    {
        $this->AutoImport = $AutoImport;
    }

    public function toDatabase($notifiable)
    {
        return [
            "id" => $this->AutoImport->id,
            "import_date" => $this->AutoImport->import_date,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            "id" => $this->AutoImport->id,
            "import_date" => $this->AutoImport->import_date,
        ]);
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }
}
