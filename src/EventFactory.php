<?php


namespace Dezsidog\Larazan;


use Dezsidog\Larazan\Events\TradeBuyerPay;
use Dezsidog\Larazan\Events\RefundSellerAgree;
use Dezsidog\Larazan\Events\RefundSuccess;
use Dezsidog\Larazan\Message\RefundNormal;
use Dezsidog\Larazan\Message\TradeNormal;

class EventFactory
{
    public static function fire(array $data)
    {
        switch ($data['type']) {
            case 'trade_TradeBuyerPay':
                event(new TradeBuyerPay(new TradeNormal($data)));
                break;
            case 'trade_refund_RefundSellerAgree':
                event(new RefundSellerAgree(new RefundNormal($data)));
                break;
            case 'trade_refund_RefundSuccess':
                event(new RefundSuccess(new RefundNormal($data)));
                break;
        }
    }
}