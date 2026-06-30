<?php

namespace App\Notifications;

use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Seller $seller,
        public string $subject,
        public string $greeting,
        public string $line,
        public ?string $actionUrl = null,
        public ?string $actionText = null,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->subject)
            ->greeting($this->greeting)
            ->line($this->line);

        if ($this->actionUrl && $this->actionText) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        $mail->line('Это уведомление отправлено от имени продавца: ' . $this->seller->name);

        return $mail;
    }
}
