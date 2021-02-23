<?php

namespace App\Listeners;

use App\Events\EventCancelledEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\UserNotification;
use App\Notifications\EventCancelled;
use Notification;

class EventCancelledListener
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
     * @param  EventCancelledEvent  $event
     * @return void
     */
    public function handle(EventCancelledEvent $event)
    {
        
        $notifications = [];
        $date = now();
        $users = [];
        foreach ($event->users as $user) {
            $temp = [];
            $temp["from_user"]      = $event->created_by;
            $temp["to_user"]        = $user->id;
            $temp["type"]           = "EVENT";
            $temp["title"]          = __("site.eventCancelled");
            $temp["message"]        = __("site.eventCancelled");
            $temp["action"]         = $event->event->id;
            $temp["created_by"]     = $event->created_by;
            $temp["updated_by"]     = $event->created_by;
            $temp["created_at"]     = $date;
            $temp["updated_at"]     = $date;
            $notifications[]        = $temp;
            $users[]                = $user->id;
        }

        UserNotification::insert($notifications);
        $title = __("site.eventCancelled");
        $message = __("site.eventCancelled");
        $data = ["type" => "EVENT"];
        $code = $event->event->code;
        Notification::send($event->users[0],new Eventcancelled($title, $message, $data, $users,$code));
    }
}
