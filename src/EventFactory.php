<?php


namespace Dezsidog\Larazan;


use Dezsidog\Larazan\Events\ReceivedTradePaid;
use Dezsidog\Larazan\Message\TradeNormal;

class EventFactory
{
    public static function fire(array $data)
    {
        switch ($data['type']) {
            case 'trade_TradeBuyerPay':
                event(new ReceivedTradePaid(new TradeNormal($data)));
                break;
        }
    }
}