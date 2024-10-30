<?php

namespace App\Notifications;

use App\Models\Process;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProcessMarkedAsReadyForOrder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'country' => $this->process->searchCountry->name,
            'manufacturer' => $this->process->manufacturer->name,
            'trademark_en' => $this->process->trademark_en,
            'trademark_ru' => $this->process->trademark_ru,
            'marketing_authorization_holder' => $this->process->marketingAuthorizationHolder?->name,
            'form' => $this->process->product->form->name,
            'pack' => $this->process->product->pack,
        ];
    }
}
