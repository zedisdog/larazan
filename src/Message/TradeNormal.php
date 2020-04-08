<?php


namespace Dezsidog\Larazan\Message;


use Illuminate\Support\Arr;

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
            default:
                throw new \Exception('no prop');
        }
    }
}