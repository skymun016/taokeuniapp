<?php

namespace app\model;

use think\Model;

class OrderCard extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_card';

    public static function timesmum($order_id)
    {
        $data =  self::where(['order_id' => $order_id])->find();
        if ($data) {
            return (int) $data->timesmum;
        }
    }
    public static function getinfobyorderid($order_id)
    {
        $data =  self::where(['order_id' => $order_id])->find();
        if ($data) {
            return $data->toArray();
        }
    }
    
}
