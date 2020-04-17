<?php


namespace Dezsidog\Larazan\Message;

use Carbon\Carbon;

/**
 * Class RefundNormal
 * @package Dezsidog\Larazan\Message
 * @property string $tid 订单号
 * @property Carbon $update_time 更新时间
 * @property string $refund_type 退款类型
 * @property string $refund_reason 退款原因
 * @property array $oids 退款子订单号
 * @property string $refund_id 退款id
 * @property int $refunded_fee 退款金额，分为单位
 */
class RefundNormal extends BaseMessageWithControlInfo
{
    /**
     * @param $name
     * @return array|Carbon|float|int|mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        switch ($name) {
            case 'update_time':
                return new Carbon($this->msg[$name]);
            case 'oids':
                return explode(',', $this->msg[$name]);
            case 'refunded_fee':
                return $this->msg[$name]*100;
            default:
                return parent::__get($name);
        }
    }
}