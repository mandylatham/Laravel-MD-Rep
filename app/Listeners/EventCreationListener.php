<?php

namespace App\Listeners;

use App\Events\EventCreationEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\UserNotification;
//use Illuminate\Notifications\Notification;
use App\Notifications\EventNotification;
use Notification;

class EventCreationListener
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
     * @param  EventCreationEvent  $event
     * @return void
     */
    public function handle(EventCreationEvent $event)
    {
        $notifications = [];
        $date = now();
        $users = [];
        if(count($event->users)>0)
        {
            
            foreach ($event->users as $user) {
                $temp = [];
                $temp["from_user"]      = $event->created_by;
                $temp["to_user"]        = $user->id;
                $temp["type"]           = "EVENT";
                $temp["title"]          = __("site.eventTitle");
                $temp["message"]        = __("site.eventMessage");
                $temp["action"]         = $event->event->id;
                $temp["created_by"]     = $event->created_by;
                $temp["updated_by"]     = $event->created_by;
                $temp["created_at"]     = $date;
                $temp["updated_at"]     = $date;
                $notifications[]        = $temp;
                $users[]                = $user->id;
            }

            UserNotification::insert($notifications);

            $title = __("site.eventTitle");
            $message = $event->event->name;
            $data = ["type" => "EVENT"];
            Notification::send($event->users[0],new EventNotification($title, $message, $data, $users));
        }
    }
}
