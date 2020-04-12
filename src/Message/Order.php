<?php


namespace Dezsidog\Larazan\Message;


class Order
{
    /**
     * @var string 商品名称
     */
    public $title;

    /**
     * @var int 商品数量
     */
    public $num;

    /**
     * @var string 商家商品编码
     */
    public $outer_item_id;

    /**
     * @var string 商家sku编码
     */
    public $outer_sku_id;

    /**
     * @var int 子订单id
     */
    public $oid;

    /**
     * @var int 有赞商品id
     */
    public $item_id;

    public function __construct(array $order_data)
    {
        foreach ($order_data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}