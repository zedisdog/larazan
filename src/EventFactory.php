<?php


namespace Dezsidog\Larazan;


use Dezsidog\Larazan\Events\ReceivedTradeCreate;
use Dezsidog\Larazan\Message\TradeNormal;

class EventFactory
{
    public static function fire(array $data)
    {
        switch ($data['type']) {
            case 'trade_TradeCreate':
                event(new ReceivedTradeCreate(new TradeNormal($data)));
                break;
        }
    }
}