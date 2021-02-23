<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $ip;
    public $deviceType;
    public $adminType;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $ip, $device, $adminType = FALSE)
    {
        $this->user         = $user;
        $this->ip           = $ip;
        $this->device_type  = $device;
        $this->adminType    = $adminType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
