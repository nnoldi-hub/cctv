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
        return config('notifications.mail_enabled') ? ['database', 'mail'] : ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Oferta ta de la CCTV Security')
            ->view('emails.offer-sent', [
                'recipientName' => $this->offer->client->name,
                'offer' => $this->offer,
                'logoUrl' => asset('branding/logo-cctv.png'),
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Oferta noua',
            'message' => 'Ai primit oferta „'.$this->offer->title.'”.',
            'offer_id' => $this->offer->id,
        ];
    }
}
