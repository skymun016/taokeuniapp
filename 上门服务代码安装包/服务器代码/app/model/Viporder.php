<?php

namespace app\model;

use think\Model;

class Viporder extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'viporder';
    static public function pay_order($params)
    {
        $order_num_alias = $params['order_num_alias'];
        $order_info = self::where(['order_num_alias' => $order_num_alias])->find();

        Member::where('id', $order_info['uid'])->update(['gid' => $order_info['gid']]);
        $MemberAG = MemberAuthGroup::find($order_info['gid']);

        if (!empty($MemberAG)) {

            $order['id'] = $order_info['id'];
            $order['pay_time'] = time();
            if ($MemberAG->start_time > 0) {
                $order['begin_time'] = strtotime("+" . $MemberAG->start_time . " hours");
            }
            if ($MemberAG->expire_day > 0) {
                $order['end_time'] = strtotime("+" . $MemberAG->expire_day . " months");
            }

            self::update($order);

            if (!empty($MemberAG->coupon_id) && !empty($MemberAG->number)) {

                $Coupondata = Coupon::where(['weid' => weid(), 'id' => $MemberAG->coupon_id])->find();
                if (!empty($Coupondata)) {
                    $Coupondata = $Coupondata->toArray();

                    unset($Coupondata['id']);
                    $Coupondata['uid'] = $order_info['uid'];
                    $Coupondata['coupon_id'] = $MemberAG->coupon_id;
                    $Coupondata['number'] = $MemberAG->number;
                    //Test::create(['info' => serialize($Coupondata)]);
                    $r = CouponReceive::create($Coupondata);
                }
            }
        }
    }
}
