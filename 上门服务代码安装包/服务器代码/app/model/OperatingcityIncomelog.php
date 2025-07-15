<?php
namespace app\model;
use think\Model;

class OperatingcityIncomelog extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'operatingcity_incomelog';

    public static function getorderIncome($order_id, $areatype)
    {
        $res = self::where('order_id', $order_id)->where('areatype', $areatype)->find();
        if (!empty($res)) {
            return  $res->income;
        } else {
            return 0;
        }
    }

}
