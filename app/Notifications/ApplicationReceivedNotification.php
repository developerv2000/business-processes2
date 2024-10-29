<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Process;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceivedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
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
            'application_id' => $this->application->id,
            'country' => $this->application->process->searchCountry->name,
            'manufacturer' => $this->application->process->manufacturer->name,
            'trademark_en' => $this->application->process->trademark_en,
            'trademark_ru' => $this->application->process->trademark_ru,
            'marketing_authorization_holder' => $this->application->process->marketingAuthorizationHolder?->name,
        ];
    }
}
