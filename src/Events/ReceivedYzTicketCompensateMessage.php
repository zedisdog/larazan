<?php
/**
 * Created by zed.
 */
declare(strict_types=1);
namespace Dezsidog\Larazan\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReceivedYzTicketCompensateMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trade_id;

    /**
     * Create a new event instance.
     *
     * @param array $data
     */
    public function __construct(string $trade_id)
    {
        $this->trade_id = $trade_id;
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
