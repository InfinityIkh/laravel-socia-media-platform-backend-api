<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Override;

class UserFollowEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $follower , public User $followed)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('follower.'.$this->follower->id.'.followed.'.$this->followed->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'follower' => [
                'id' => $this->follower->id,
                'name' => $this->follower->name 
            ],
            'followed' => [
                'id' => $this->followed->id,
                'name' => $this->followed->name 
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.follow';
    }
}
