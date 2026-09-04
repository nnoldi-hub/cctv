<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferSent extends Notification
{
    use Queueable;

    public function __construct(public Offer $offer)
    {
        //
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Oferta ta de la CCTV Security')
            ->greeting('Buna, '.$this->offer->client->name.'!')
            ->line('Ti-am pregatit o oferta pentru sistemul de supraveghere video.')
            ->line('Titlu: '.$this->offer->title)
            ->line('Valoare estimata: '.number_format((float) $this->offer->total_amount, 2).' lei')
            ->line('Pentru detalii complete, te rugam sa ne contactezi.')
            ->salutation('Cu stima, echipa CCTV Security');
    }
}
