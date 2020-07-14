<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallVideo implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $receiver;
    public $sender_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver, $sender_id)
    {
        $this->receiver = $receiver;
        $this->sender_id = $sender_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['video-call.'. $this->receiver];
    }

    public function broadcastAs()
    {
        return 'VideoCall';
    }
    public function broadcastWith()
    {
        return ["sender_id" => $this->sender_id];
    }
}
