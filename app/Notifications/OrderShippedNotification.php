<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification implements ShouldQueue
{
use Queueable;

public function __construct(public Order $order) {}

public function via($notifiable): array
{
// можно вернуть ['mail', 'sms', 'database'] и т.д.
return ['mail'];
}

public function toMail($notifiable): MailMessage
{
return (new MailMessage)
->from('no-reply@brauniartshop.com', 'BrauniArtShop')
->subject('Заказ отправлен')
->greeting('Здравствуйте, ' . $notifiable->name . '!')
->line('Ваш заказ №' . $this->order->id . ' отправлен.')
->action('Отслеживание', $this->order->tracking_url ?? '#')
->line('Спасибо за покупку.');
}

// toDatabase(), toSlack() и др. — по необходимости
}
