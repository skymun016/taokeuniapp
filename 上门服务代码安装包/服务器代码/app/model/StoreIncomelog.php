<?php
namespace app\model;
use think\Model;

class StoreIncomelog extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'store_incomelog';

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
