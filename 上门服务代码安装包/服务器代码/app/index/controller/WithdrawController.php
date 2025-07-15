<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Withdraw;
use app\model\UuidRelation;
use app\model\Agent;
use app\model\Technical;
use app\model\Tuanzhang;
use app\model\Store;
use app\model\Operatingcity;
use app\model\Config;

class WithdrawController extends Base
{
    public function index()
    {

        $where['weid'] = weid();
        $where['uid'] = UID();

        $data = Withdraw::where($where)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($data as &$vo) {
            if (!empty($vo['pay_time'])) {
                $vo['pay_time'] = time_format($vo['pay_time']);
            }
        }

        return $this->json(['data' => $data]);
    }

    public function calculate()
    {
        $message = '返回消息';
        $errno = 0;
        $amounts = input('post.amounts', '', 'serach_in');
        $mo = input('post.mo', '', 'serach_in');
        $Config = Config::getconfig($mo);
        if (empty($Config['cash_service_charge'])) {
            $data['poundage'] = 0;
            $data['poundage_rate'] = $Config['cash_service_charge'];
            $data['actual_amounts'] = $amounts;
        } else {
            $data['poundage'] = $amounts * percent_to_num($Config['cash_service_charge']);
            $data['poundage_rate'] = $Config['cash_service_charge'];
            $data['actual_amounts'] = $amounts - $data['poundage'];
        }
        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }
    public function apply()
    {
        $message = '返回消息';
        $data = [];
        $uid = UID();       
        $amounts = input('post.amounts', '', 'serach_in');
        $bid = input('post.bid', '', 'serach_in');
        $pay_from = input('get.from', '', 'serach_in');
        $mo = input('post.mo', '', 'serach_in');
        $uuid = UuidRelation::getuuid(UID(), $mo);
        $Config = Config::getconfig($mo);

        if ($mo == 'agent') {
            $moapply =  Agent::where('uid', $uid)->find();
            $moapplyid = $moapply->id;
            $moapplyincome = $moapply->income;
            $model =  new Agent;
        } elseif ($mo == 'technical') {
            $moapply =  Technical::where('uuid', $uuid)->find();
            $moapplyid = $moapply->id;
            $moapplyincome = $moapply->income;
            $model =  new Technical;
        }elseif ($mo == 'tuanzhang') {
            $moapply =  Tuanzhang::where('uuid', $uuid)->find();
            $moapplyid = $moapply->id;
            $moapplyincome = $moapply->income;
            $model =  new Tuanzhang;
        } elseif ($mo == 'store') {
            $moapply = Store::getInfobyuid(UID());
            $moapplyincome = $moapply['income'];
            $moapplyid = $moapply['id'];
            $model =  new Store;
        } elseif ($mo == 'operatingcity') {
            $moapply =  Operatingcity::where('uuid', $uuid)->find();
            $moapplyid = $moapply->id;
            $moapplyincome = $moapply->income;
            $model =  new Operatingcity;
        }

        if ($moapplyincome >= $amounts) {
            $param['withdraw_sn'] = $this->build_no();
            $param['mo'] = $mo;
            $param['amounts'] = $amounts;
            $param['actual_amounts'] = $amounts;
            if (empty($Config['cash_service_charge'])) {
                $param['poundage'] = 0;
            } else {
                $param['poundage'] = $amounts * percent_to_num($Config['cash_service_charge']);
                $param['poundage_rate'] = $Config['cash_service_charge'];
                $param['actual_amounts'] = $amounts - $param['poundage'];
            }

            $param['status'] = 0;
            $param['uid'] = $uid;
            $param['bid'] = $bid;
            $param['pay_from'] = $pay_from;
            $param["weid"] = weid();

            $Withdraw =   Withdraw::create($param);

            if ($Withdraw) {
                $model->where('id', $moapplyid)->dec('income', $amounts)->update();
                $errno = 0;
                $message = '您的提现申请己提交';
            } else {
                $errno = 1;
                $message = '申请提现失败，请联系管理员';
            }
        } else {
            $errno = 1;
            $message = '申请提现失败，提现金额不能大于帐户余额';
        }

        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }

    //生成唯一编号
    function build_no()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}
