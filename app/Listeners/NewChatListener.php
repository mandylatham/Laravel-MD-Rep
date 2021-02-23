<?php

namespace App\Listeners;

use App\Events\NewChatEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\NewChatMessage;
use Notification;

class NewChatListener
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
     * @param  NewChatEvent  $event
     * @return void
     */
    public function handle(NewChatEvent $event)
    {
        $userId                 = $event->userId;
        $chatData               = $event->data;
        $data["message"]        = $chatData;
        $data["chat_group"]     = $event->chatGroup;
        $data["from_user"]      = auth()->user();
        $data["type"]           = "CHAT_MESSAGE";
        $users                  = [$userId];

        $title                  = auth()->user()->name;
        if($chatData->type == config("site.chat_type.text")){
            $message                = $chatData->message;
        }else{
            $message                  = __("site.chatImage");
        }
        Notification::send($event->userId,new NewChatMessage($title, $message, $data, $users));
    }
}
