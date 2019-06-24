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
    public $shop_id;

    /**
     * Create a new event instance.
     *
     * @param string $trade_id
     * @param string $shop_id
     */
    public function __construct(string $trade_id, string $shop_id)
    {
        $this->trade_id = $trade_id;
        $this->shop_id = $shop_id;
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
