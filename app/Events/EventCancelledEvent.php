<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventCancelledEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $users;
    public $created_by;
    public $event;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($users,$event,$created_by)
    {
        $this->users = $users;
        $this->event = $event;
        $this->created_by = $created_by;
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
