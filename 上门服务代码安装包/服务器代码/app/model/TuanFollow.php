<?php

namespace app\model;

use think\Model;

class TuanFollow extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'tuan_follow';

    static public function add_follow($param)
    {
        $Membermob = new Member;
        $Member = $Membermob->getUserByWechat();
        $TuanFound = TuanFound::find($param['found_id']);

        $data['uid'] = $Member['id'];
        $data['nickname'] = $Member['nickname'];
        $data['avatar'] = $Member['userpic'];
        $data['join_time'] = time();
        $data['order_id'] = (int) $param['order_id'];
        $data['found_id'] = (int) $param['found_id'];
        $data['tuan_id'] = (int) $param['tuan_id'];
        $data['is_head'] = (int) $param['is_head'];
        $data['is_robot'] = (int) $param['is_robot'];

        $data['tuan_end_time'] = $TuanFound->tuan_end_time;

        $res = self::create($data);
        return $res->id;
    }

    static public function getTuanFollow($found_id)
    {
        $FollowList = self::where('found_id', $found_id)->select()->toArray();
        return  $FollowList;
    }

    static public function getLuckyFollow($found_id, $luckydraw_num)
    {

        $Followlist = self::where('found_id', $found_id)->where('is_robot', '<>', 1)->where('pay_time', '>', 0)->select()->toArray();
        for ($i = 0; $i < $luckydraw_num; $i++) {
            $resList[$i] = $Followlist[mt_rand(0, count($Followlist) - 1)];
        }

        return  $resList;
    }

    static public function setRefund()
    {
        $Followlist = self::where('is_refund', 0)->where('status', 2)->where('is_robot', '<>', 1)->where('pay_time', '>', 0)->select()->toArray();

        if (!empty($Followlist)) {

            foreach ($Followlist as $vo) {
                Order::refund_order($vo['order_id'], time());
                Order::update(['order_status_id' => 6, 'id' => $vo['order_id']]);
                self::setNotWinning($vo);
                self::where('id', $vo['id'])->update(['is_refund' => 1]);
            }
        }
    }
    static public function setNotWinning($Follow)
    {
        $TuanGoods = TuanGoods::find($Follow['tuan_id']);
        $orderInfo = Order::find($Follow['order_id']);
        if (!empty($TuanGoods) && !empty($orderInfo)) {
            $TuanGoods = $TuanGoods->toArray();
            $orderInfo = $orderInfo->toArray();
            if ($TuanGoods['not_winning_ptype'] == 2) {
                Member::where('id', $Follow['uid'])
                    ->inc('balance', $TuanGoods['not_winning_redenvelope'])
                    ->update();
                $cashlogsdata['uid'] = $Follow['uid'];
                $cashlogsdata['weid'] = weid();
                $cashlogsdata['order_id'] = $Follow['order_id'];
                $cashlogsdata['order_num_alias'] = $orderInfo['order_num_alias'];
                $cashlogsdata['remarks'] = '拼团没拼中，平台发红包';
                $cashlogsdata['prefix'] = 1;
                $cashlogsdata['amount'] = $TuanGoods['not_winning_redenvelope'];
                MemberCashlogs::create($cashlogsdata);
            } else if ($TuanGoods['not_winning_ptype'] == 3) {
                $coupon_id = $TuanGoods['not_winning_coupon_id'];
                $Coupondata = Coupon::where(['weid' => weid(), 'id' => $coupon_id])->find();
                if (!empty($Coupondata)) {
                    $Coupondata = $Coupondata->toArray();
                    if ($Coupondata['total_num'] == -1 || $Coupondata['total_num'] > $Coupondata['receive_num']) {
                        unset($Coupondata['id']);
                        $Coupondata['uid'] = $Follow['uid'];
                        $Coupondata['coupon_id'] = $coupon_id;
                        $r = CouponReceive::create($Coupondata);
                        if ($r) {
                            $receive_num = $Coupondata['receive_num'] + 1;
                            Coupon::update(['id' => $coupon_id, 'receive_num' => $receive_num]);
                        }
                    }
                }

            } else if ($TuanGoods['not_winning_ptype'] == 4) {
                Points::create([
                    'weid' => weid(),
                    'uid' => $Follow['uid'],
                    'order_id' => $Follow['order_id'],
                    'order_num_alias' => $orderInfo['order_num_alias'],
                    'points' => $TuanGoods['not_winning_points'],
                    'description' => '拼团没拼中，平台送积分',
                    'prefix' => 1,
                    'creat_time' => time(),
                    'type' => 1
                ]);

                Member::where('id', $Follow['uid'])
                    ->inc('points', (int) $TuanGoods['not_winning_points'])
                    ->update();
            }
        }
    }
}
