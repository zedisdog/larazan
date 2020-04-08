<?php


namespace Dezsidog\Larazan\Message;

use Illuminate\Support\Arr;

/**
 * Class BaseMessageWithControlInfo
 * @package Dezsidog\Larazan\Message
 * 有赞消息推送格式多变，这只其中一个基础格式
 */
class BaseMessageWithControlInfo
{
    /**
     * @var array 消息体
     */
    public $msg;

    /**
     * @var int 重发次数
     */
    public $send_count;

    /**
     * @var int
     */
    public $mode;

    /**
     * @var string
     */
    public $app_id;

    /**
     * @var int
     */
    public $client_id;

    /**
     * @var int
     * 消息版本号,为了解决顺序性的问题,高版本覆盖低版本
     */
    public $version;

    /**
     * @var string
     * 消息业务类型
     */
    public $type;

    /**
     * @var int
     * 业务消息的标识
     */
    public $id;

    /**
     * @var string
     * 防伪签名: MD5(client_id+msg+client_secrect)。
     * 不建议使用此字段，可以用请求头中的Event-Sign进行防伪验证
     */
    public $sign;

    /**
     * @var int
     */
    public $kdt_id;

    /**
     * @var bool false-非测试消息，true-测试消息
     */
    public $test;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string 店铺名称
     */
    public $kdt_name;

    /**
     * @var string 消息唯一标示
     */
    public $msg_id;

    public function __construct(array $message)
    {
        foreach ($message as $key => $value) {
            switch ($key) {
                case 'sendCount':
                    $this->send_count = $value;
                    break;
                case 'msg':
                    $this->setMsg($value);
                    break;
                default:
                    $this->$key = $value;
                    break;
            }
        }
    }

    protected function setMsg($value)
    {
        $data = json_decode(urldecode($value), true);
        if(Arr::exists($data, 'full_order_info.orders')){
            $orders = [];
            foreach (Arr::get($data, 'full_order_info.orders') as $order_data) {
                $orders[] = new Order($order_data);
            }
            $data['full_order_info']['orders'] = $orders;
        }
        $this->msg = $data;
    }
}