<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcceptCall implements ShouldBroadcast
{
    public $idCall;
    
    public $inviter;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($idCall, $inviter)
    {
        $this->idCall = $idCall;
        $this->inviter= $inviter;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['accept-call.'. $this->inviter];
    }

    public function broadcastAs()
    {
        return 'AcceptCall';
    }
    public function broadcastWith()
    {
        return ["idCall" => $this->idCall];
    }
}
