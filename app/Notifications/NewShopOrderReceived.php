<?php

namespace App\Notifications;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewShopOrderReceived extends Notification
{
    use Queueable;

    public function __construct(public ShopOrder $order)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Comanda noua din magazin: '.$this->order->order_number)
            ->line('A fost plasata o noua comanda in magazinul online.')
            ->line('Numar comanda: '.$this->order->order_number)
            ->line('Client: '.$this->order->name.' ('.$this->order->phone.')')
            ->line('Total: '.number_format((float) $this->order->total, 2).' lei')
            ->action('Vezi comanda', route('admin.shop-orders.show', $this->order));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => (float) $this->order->total,
        ];
    }
}
