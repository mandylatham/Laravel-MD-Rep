<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\LoginHistory;
use App\AdminLoginHistory;

class LoginListener
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
     * @param  LoginEvent  $event
     * @return void
     */
    public function handle(LoginEvent $event)
    {
        if($event->adminType){
            $login_history = new AdminLoginHistory();
        }else{
            $login_history = new LoginHistory();
        }
        $login_history->user_id     = $event->user->id;
        $login_history->device_type = $event->deviceType;
        $login_history->ip          = $event->ip;
        $login_history->save();
    }
}
