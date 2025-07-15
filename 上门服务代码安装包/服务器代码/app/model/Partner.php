<?php

namespace app\model;

use think\Model;

class Partner extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'partner';

    public static function is_agent($uid)
    {
        $data = Partner::where(['uid' => $uid, 'status' => 1])->find();
        if (!empty($data)) {
            return 1;
        } else {
            return 0;
        }
    }
    public static function getTitle($uid)
    {
        $data = Partner::where(['uid' => $uid])->find();
        if (!empty($data)) {
            return $data->title;
        }
    }
    public static function register($Member)
    {
        if (empty($Member['id'])) {
            return;
        }
        $weid = weid();
        $data = Partner::where(['uid' => $Member['id'], 'weid' => $weid])->find();

        if (!empty($data)) {
            Partner::where(['id' => $data->id])->update(['status' => 1]);
        } else {
            $createdata['weid'] = $weid;
            $createdata['uid'] = $Member['id'];
            $createdata['title'] = $Member['nickname'];
            $createdata['level'] = (int) PartnerLevel::getdefault()['id'];
            $createdata['status'] = 1;

            Partner::create($createdata);
        }
    }
    public static function conversion($vo)
    {
        $vo['level'] = PartnerLevel::getLevel($vo['level']) ?? '初级';
        if (!empty($vo['uuid'])) {
            $vo['username'] = Users::getusername($vo['uuid']);
        }
        $vo = RegisterField::conversion($vo);
        return $vo;
    }

    public static function setIncome($uid, $order_info, $percent, $level)
    {
        $Member = Member::find($uid);
        if (!empty($Member)) {
            $Member = $Member->toArray();
            $Member['is_agent'] = Partner::is_agent($Member['id']);
        }

        //代理商是状态是开启的
        if ($Member['is_agent'] == 1) {
            $return_percent = $percent;

            $agent_level = PartnerLevel::find($Member['agent_level']);
            if (!empty($agent_level)) {
                $agent_level = $agent_level->toArray();
            }

            if (!empty($agent_level['return_percent'])) {
                $income = $income * percent_to_num($agent_level['return_percent']);
                $return_percent = $return_percent * percent_to_num($agent_level['return_percent']);
            }

            if ($income > 0 && $income < 0.01) {
                $income = 0.01;
            }
            Partner::where('uid', $uid)->inc('income', $income)->update();
            Partner::where('uid', $uid)->inc('total_income', $income)->update();

            $incomedata['uid'] = $uid;
            $incomedata['ptype'] = 1;
            $incomedata['weid'] = weid();
            $incomedata['level'] = $level;
            $incomedata['order_id'] = $order_info['id'];
            $incomedata['order_num_alias'] = $order_info['order_num_alias'];
            $incomedata['buyer_id'] = $order_info['uid'];
            $incomedata['income'] = $income;
            $incomedata['return_percent'] = floatval($return_percent);
            $incomedata['percentremark'] = $percent . '% x' . $agent_level['return_percent'] . '%';
            $incomedata['order_total'] = $order_info['total'];
            $incomedata['pay_time'] = $order_info['pay_time'];
            $incomedata['month_time'] = date('m', time());
            $incomedata['year_time'] = date('Y', time());
            $incomedata['order_status_id'] = 2; //已付款

            Incomelog::create($incomedata);
        }
    }
}
