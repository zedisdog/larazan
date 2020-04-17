<?php


namespace Dezsidog\Larazan\Events;


use Dezsidog\Larazan\Message\TradeNormal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TradeBuyerPay
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var TradeNormal
     */
    public $message;

    public function __construct(TradeNormal $message)
    {
        $this->message = $message;
    }
}