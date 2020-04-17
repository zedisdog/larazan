<?php


namespace Dezsidog\Larazan\Events;


use Dezsidog\Larazan\Message\RefundNormal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundSellerAgree
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var RefundNormal
     */
    public $message;

    public function __construct(RefundNormal $message)
    {
        $this->message = $message;
    }
}