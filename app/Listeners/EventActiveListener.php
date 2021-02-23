<?php

namespace App\Listeners;

use App\Events\EventActiveEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\UserNotification;
use App\Notifications\EventActivated;
use Notification;

class EventActiveListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EventActiveEvent  $event
     * @return void
     */
    public function handle(EventActiveEvent $event)
    {
        $notifications = [];
        $date = now();
        $users = [];
        foreach ($event->users as $user) {
            $temp = [];
            $temp["from_user"]      = $event->created_by;
            $temp["to_user"]        = $user->id;
            $temp["type"]           = "EVENT";
            $temp["title"]          = __("site.eventActivated");
            $temp["message"]        = __("site.eventActivated");
            $temp["action"]         = $event->event->id;;
            $temp["created_by"]     = $event->created_by;
            $temp["updated_by"]     = $event->created_by;
            $temp["created_at"]     = $date;
            $temp["updated_at"]     = $date;
            $notifications[]        = $temp;
            $users[]                = $user->id;
        }

        UserNotification::insert($notifications);
        $title = __("site.eventActivated");
        $message = $event->event->name;;
        $data = ["type" => "EVENT"];
        $code = $event->event->code;
        Notification::send($event->users[0],new EventActivated($title, $message, $data, $users,$code));
    }
}
