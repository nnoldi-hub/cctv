<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Offer $offer,
        public string $status,
        public ?string $message = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return config('notifications.mail_enabled') ? ['database', 'mail'] : ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Raspuns client la oferta',
            'message' => 'Oferta „'.$this->offer->title.'” a fost '.($this->status === 'accepted' ? 'acceptata' : 'respinsa').'.',
            'offer_id' => $this->offer->id,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Raspuns client la oferta #'.$this->offer->id)
            ->greeting('Buna, '.$notifiable->name.'!')
            ->line('Clientul a '.($this->status === 'accepted' ? 'acceptat' : 'respins').' oferta „'.$this->offer->title.'”.')
            ->action('Vezi oferta', route('sales.offers.show', $this->offer))
            ->salutation('Cu stima, CCTV Security');

        if ($this->message) {
            $mail->line('Mesaj client: '.$this->message);
        }

        return $mail;
    }
}
