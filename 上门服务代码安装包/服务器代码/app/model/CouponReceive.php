<?php
namespace app\model;

use think\Model;

class CouponReceive extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'coupon_receive';

    public static function getReceiveState($coupon_id, $uid) {

        $State = self::where(['uid' => $uid, 'coupon_id' => $coupon_id])->find();
		if (!empty($State)) {
			$State = $State->toArray();
		}

        if (empty($State)) {
            $retdata['value'] = 1;
            $retdata['text'] = '立即领取';
        } else {
            $retdata['value'] = 0;
            $retdata['text'] = '已领取';
        }
        return $retdata;
    }

}
