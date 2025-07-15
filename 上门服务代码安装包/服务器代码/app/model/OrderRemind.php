<?php

namespace app\model;

use think\Model;

class OrderRemind extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_remind';

    public static function neworder($order)
    {
        $data['weid'] = weid();
        $data['order_id'] = $order['id'];
        $data['content'] = '刚刚下了订单';
        $res = self::create($data);
    }
}
