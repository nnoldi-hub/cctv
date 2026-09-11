<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OperationalReminder extends Notification
{
    use Queueable;

    public function __construct(
        public string $kind,
        public string $title,
        public string $message,
        public ?string $url = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
