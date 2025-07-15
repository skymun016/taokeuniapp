<?php

namespace app\model;

use think\Model;

class TechnicalIncomelog extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'technical_incomelog';

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'buyer_id');
    }
    public function technical()
    {
        return $this->hasOne(Technical::class, 'uuid', 'uuid');
    }
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id');
    }

    public static function getorderIncome($order_id)
    {
        $res = self::where('order_id', $order_id)->find();
        if (!empty($res)) {
            return  $res->income;
        }else{
            return 0;
        }
    }
}
