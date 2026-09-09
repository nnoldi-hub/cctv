<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $message,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Actualizare cerere #'.$this->ticket->id)
            ->greeting('Buna, '.$notifiable->name.'!')
            ->line($this->message)
            ->line('Cerere: '.$this->ticket->subject)
            ->action('Vezi portalul client', route('client.tickets.index'))
            ->salutation('Cu stima, echipa CCTV Security');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Actualizare cerere',
            'message' => $this->message,
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
        ];
    }
}
