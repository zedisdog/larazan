<?php


namespace Dezsidog\Larazan\Message;


use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Class TradeNormal
 * @package Dezsidog\Larazan\Message
 * @property-read string $tid 订单id
 * @property-read string $status 定单状态
 * @property-read Order[] $orders 子订单
 * @property-read string $receiver_name 收货人姓名
 * @property-read string $receiver_tel 收货人联系电话
 * @property-read string $delivery_address 收货详细地址
 * @property-read string $delivery_province 收货省
 * @property-read string $delivery_city 收货市
 * @property-read string $delivery_district 收货区
 * @property-read Carbon|null $pay_time 支付时间
 */
class TradeNormal extends BaseMessageWithControlInfo
{
    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        switch ($name) {
            case 'tid':
                return Arr::get($this->msg, 'full_order_info.order_info.tid');
            case 'status':
                return Arr::get($this->msg, 'full_order_info.order_info.status');
            case 'orders':
                return Arr::get($this->msg, 'full_order_info.orders');
            case 'receiver_name':
                return Arr::get($this->msg, 'full_order_info.address_info.receiver_name');
            case 'receiver_tel':
                return Arr::get($this->msg, 'full_order_info.address_info.receiver_tel');
            case 'delivery_address':
                return Arr::get($this->msg, 'full_order_info.address_info.delivery_address');
            case 'delivery_province':
                return Arr::get($this->msg, 'full_order_info.address_info.delivery_province');
            case 'delivery_city':
                return Arr::get($this->msg, 'full_order_info.address_info.delivery_city');
            case 'delivery_district':
                return Arr::get($this->msg, 'full_order_info.address_info.delivery_district');
            case 'payment_time':
                $time = Arr::get($this->msg, 'full_order_info.order_info.pay_time');
                return $time ? new Carbon($time) : null;
            default:
                throw new \Exception('no prop');
        }
    }
}