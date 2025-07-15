<?php

namespace app\model;

use think\Model;

class Points extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'points';

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'uid');
    }

    public static function getdescription($item)
    {
        $data['addcustomer'] = "新增客户";
        $data['adddongtai'] = "写跟进";
        $data['upjieduan'] = "推进客户";
        $data['deal'] = "成交客户";

        return $data[$item];
    }

    public static function uppoints($params)
    {
        $item = $params['item'];
        $uid = $params['uid'];
        $cid = $params['cid'];

        $pointsConfig =  Config::getconfig('points');
        $points = (int)$pointsConfig[$item];
        //更新积分
        if ($points) {

            self::create([
                'weid' => weid(),
                'uid' => $uid,
                'cid' => $cid,
                'points' => $points,
                'description' => self::getdescription($item),
                'prefix' => 1,
                'creat_time' => time(),
                'type' => 1
            ]);

            Users::where('id', $uid)->inc('points', (int) $points)->update();
        }
    }
}
