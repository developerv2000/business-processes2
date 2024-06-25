<?php

namespace App\Notifications;

use App\Models\Process;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProcessContractStage extends Notification
{
    use Queueable;

    public $process;

    /**
     * Create a new notification instance.
     */
    public function __construct(Process $process, $newStatusName)
    {
        $this->process = $process;
        $this->status_name = $newStatusName;
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
            'process_id' => $this->process->id,
            'status_name' => $this->status_name,
        ];
    }
}
