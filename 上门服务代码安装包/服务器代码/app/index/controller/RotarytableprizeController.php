<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\RotarytablePrize;
use app\model\RotarytableLog;
use app\model\Member;
use app\model\CouponReceive;
use app\model\Coupon;
use app\model\Points;
use app\model\Config;

class RotarytableprizeController extends Base
{
    public function index()
    {
        $config = Config::getconfig('rotarytable');
        $data['config'] = $config;
        $prizelist = RotarytablePrize::where(['weid' => weid()])->order('sort asc,id desc')->select()->toArray();

        for ($i = 0; $i < count($prizelist); $i++) {
            $data['prizelist'][$i]['prizeId'] = $prizelist[$i]['id'];
            $data['prizelist'][$i]['prizeName'] = $prizelist[$i]['title'];
            $data['prizelist'][$i]['prizeWeight'] = $prizelist[$i]['probability'];
            $data['prizelist'][$i]['prizeImage'] = $prizelist[$i]['image'];
        }

        foreach ($data['prizelist'] as $vo) {
            if ($vo['quantity'] > 0) {
                $prizeWeight[$vo['prizeId']] = $vo['prizeWeight'];
            }
        }

        $prizeId = $this->get_rand($prizeWeight); //根据概率获取奖项id

        $data['prizeId'] = $prizeId;

        return $this->json(['data' => $data]);
    }

    function drawstart()
    {
        $uid = UID();
        $config = Config::getconfig('rotarytable');

       
        if ($config['participate_rules'] == 1) {
            $query =  RotarytableLog::where(['uid' => $uid, 'weid' => weid()]);
            $query->where('create_time', '>=', strtotime(date('Y-m-d')));
            $query->where('create_time', '<=', strtotime(date('Y-m-d')) + 86400);

            $participate_num = $query->count();

            if ($participate_num >= $config['participate_num']) {
                return $this->json(['errno' => 1, 'msg' => '您今天的抽奖机会已经用完了']);
            }
        } else {
            $query =  RotarytableLog::where(['weid' => weid()]);
            $query->where('create_time', '>=', strtotime(date('Y-m-d')));
            $query->where('create_time', '<=', strtotime(date('Y-m-d')) + 86400);

            $participate_num = $query->count();
            if ($participate_num >= $config['participate_num']) {
                return $this->json(['errno' => 1, 'msg' => '今天的抽奖机会已经用完了，请明天再来吧']);
            }
        }

        $member = Member::find($uid);
        if (!empty($member)) {
            if ($member['points'] < $config['pay_points']) {
                return $this->json(['errno' => 1, 'msg' => '您的积分不足']);
            }else{
                Member::where('id', $uid)
                ->dec('points', $config['pay_points'])
                ->update();
            }
        }

        return $this->json(['data' => $data]);
    }


    function getprizeid()
    {

        $uid = UID();
        $config = Config::getconfig('rotarytable');
        $prizelist = RotarytablePrize::where(['weid' => weid()])->order('sort asc,id desc')->select()->toArray();
        foreach ($prizelist as $vo) {
            $prizeWeight[$vo['id']] = $vo['probability'];
        }

        $prizeId = $this->get_rand($prizeWeight); //根据概率获取奖项id
        $data['prizeId'] = $prizeId;

        foreach ($prizelist as $vo) {
            if ($vo['id'] == $prizeId) {

                RotarytableLog::create([
                    'uid' => $uid,
                    'weid' => weid(),
                    'rid' => 0,
                    'ptype' => $vo['ptype'],
                    'title' => $vo['title'],
                    'image' => $vo['image'],
                    'price' => $vo['price'],
                    'points' => $vo['points'],
                    'coupon_id' => $vo['coupon_id']
                ]);

                if ($vo['ptype'] == 2) {
                    Member::where('id', $uid)
                        ->inc('balance', $vo['price'])
                        ->update();
                }

                if ($vo['ptype'] == 3 && $vo['coupon_id']) {
                    $Coupondata = Coupon::where(['weid' => weid(), 'id' => $vo['coupon_id']])->find();

                    if (!empty($Coupondata)) {
                        $Coupondata = $Coupondata->toArray();
                        unset($Coupondata['id']);
                        $Coupondata['uid'] = $uid;
                        $Coupondata['coupon_id'] = $vo['coupon_id'];
                        CouponReceive::create($Coupondata);
                    }
                }

                if ($vo['ptype'] == 4 && $vo['points'] > 0) {
                    Points::create([
                        'weid' => weid(),
                        'uid' => $uid,
                        'order_id' => 0,
                        'points' => $vo['points'],
                        'description' => '抽奖得积分',
                        'prefix' => 1,
                        'creat_time' => time(),
                        'type' => 3
                    ]);

                    Member::where('id', $uid)
                        ->inc('points', (int) $vo['points'])
                        ->update();
                }
            }
        }

        return $this->json(['data' => $data]);
    }

    function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }
}
