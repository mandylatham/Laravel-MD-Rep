<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\FcmChannel;

class EventNotification extends Notification
{
    use Queueable;

    private $title;
    private $message;
    private $data;
    private $users;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $message, $data, $users)
    {
        $this->title    = $title;
        $this->message  = $message;
        $this->data     = $data;
        $this->users    = $users;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toFCM($notifiable)
    {
        return ["title" => $this->title, "message" => $this->message, "data" => $this->data, "users" => $this->users];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
