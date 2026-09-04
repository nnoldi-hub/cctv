<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadReceived extends Notification
{
    use Queueable;

    public function __construct(public Client $client)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Lead nou: '.$this->client->name)
            ->line('A fost primita o noua cerere de oferta de pe site.')
            ->line('Nume: '.$this->client->name)
            ->line('Telefon: '.$this->client->phone)
            ->line('Oras: '.($this->client->city ?? '-'))
            ->line('Mesaj: '.($this->client->notes ?? '-'))
            ->action('Vezi in CRM', route('sales.dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'client_id' => $this->client->id,
            'name' => $this->client->name,
            'phone' => $this->client->phone,
        ];
    }
}
