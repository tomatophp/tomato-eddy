<?php

namespace TomatoPHP\TomatoEddy\Notifications;

use TomatoPHP\TomatoEddy\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobOnServerFailed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Server $server, public string $reference)
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
            ->error()
            ->subject(__('Job on server failed'))
            ->line(__("We tried to run a job on your server, but it failed. Here's what we tried to do:"))
            ->line(Markdown::parse($this->reference))
            ->action(__('View Server'), route('admin.servers.show', $this->server));
    }
}
