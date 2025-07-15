<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Signin;
use app\model\Points;
use app\model\SigninConfig;
use app\model\Member;

class SigninController extends Base
{

    public function list()
    {
        $where['weid'] = weid();
        $where['uid'] = UID();
        $data = Signin::where($where)
            ->order('id desc')
            ->select()
            ->toArray();

        return $this->json(['data' => $data]);
    }
    public function add()
    {
        if (!Signin::getIsDaySgin()) {
            try {
                $number = Signin::getSginNumber();
                $uid = UID();
                $weid = weid();
                $SumSginDay = Signin::getSumSginDay() + 1;
                $description = '连续签到' . $SumSginDay . '天奖励积分';

                Signin::create([
                    'weid' => $weid,
                    'uid' => $uid,
                    'number' => (int) $number,
                    'title' => $description
                ]);

                Points::create([
                    'weid' => $weid,
                    'uid' => $uid,
                    'points' => (int) $number,
                    'description' => $description,
                    'prefix' => 1,
                    'type' => 3
                ]);

                Member::where('id', $uid)
                    ->inc('points', (int) $number)
                    ->update();
            } catch (\Exception $e) {
                $res['errno'] = 1;
                $res['msg'] = $e->getMessage();
            }
        } else {
            $res['errno'] = 1;
            $res['msg'] = '你今天已签到！';
        }

        $memberinfo = Member::where('id', $uid)->find();
        if ($memberinfo) {
            $memberinfo = $memberinfo->toArray();
        }
        $memberinfo['sign_index'] = Signin::getSignIndex();
        $memberinfo['is_day_sgin'] = Signin::getIsDaySgin();
        $memberinfo['sum_sgin_day'] = Signin::getSumSginDay();
        $memberinfo['getpoints'] = $number;
        $res['data'] = $memberinfo;
        return $this->json($res);
    }
    public function memberinfo()
    {
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();
        $memberinfo['sign_index'] = Signin::getSignIndex();
        $memberinfo['is_day_sgin'] = Signin::getIsDaySgin();
        $memberinfo['sum_sgin_day'] = Signin::getSumSginDay();



        return $this->json(['data' => $memberinfo]);
    }

    public function signinconfig()
    {
        $where['weid'] = weid();
        $data = SigninConfig::where($where)
            ->order('sort asc')
            ->select()
            ->toArray();


        return $this->json(['data' => $data]);
    }
}
