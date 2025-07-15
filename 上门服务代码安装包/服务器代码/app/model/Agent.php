<?php

namespace app\model;

use think\Model;

class Agent extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'agent';

    public static function is_agent($uid)
    {
        $data = Agent::where(['uid' => $uid, 'status' => 1])->find();
        if (!empty($data)) {
            return 1;
        } else {
            return 0;
        }
    }
    public static function getTitle($uid)
    {
        $data = Agent::where(['uid' => $uid])->find();
        if (!empty($data)) {
            return $data->title;
        }
    }
    public static function conversion($vo)
    {
        $vo['agent_level'] = AgentLevel::getLevel($vo['agent_level']) ?? '初级';
        $vo = RegisterField::conversion($vo);
        return $vo;
    }

    public static function register($Member)
    {
        if (empty($Member['id'])) {
            return;
        }
        $weid = weid();
        $data = Agent::where(['uid' => $Member['id'], 'weid' => $weid])->find();

        if (!empty($data)) {
            Agent::where(['id' => $data->id])->update(['status' => 1]);
        } else {
            $createdata['weid'] = $weid;
            $createdata['uid'] = $Member['id'];
            $createdata['title'] = $Member['nickname'];
            $createdata['agent_level'] = (int) AgentLevel::getdefault()['id'];
            $createdata['status'] = 1;

            Agent::create($createdata);
        }
    }

    public static function setIncome($uid, $order_info, $percent, $level)
    {
        $Agent = Agent::where('uid', $uid)->find();

        //代理商是状态是开启的
        if (!empty($Agent)) {
            $share = Config::getconfig('share');
            $Agent = $Agent->toArray();

            if ($share['price_type'] == 1) {
                $income = $percent;
            } else {
                $income = OrderGoods::getCommission($order_info, 'agent', $percent);
            }
            $return_percent = $percent;

            $agent_level = AgentLevel::find($Agent['agent_level']);
            if (!empty($agent_level)) {
                $agent_level = $agent_level->toArray();
            }

            if (!empty($agent_level['return_percent'])) {
                $income = $income * percent_to_num($agent_level['return_percent']);
                $return_percent = $return_percent * percent_to_num($agent_level['return_percent']);
            }
            if ($income > 0) {

                if ($income > 0 && $income < 0.01) {
                    $income = 0.01;
                }
                $Incomelog = Incomelog::where([
                    'uid' => $uid,
                    'level' => $uid,
                    'weid' => $level,
                    'order_id' =>  $order_info['id'],
                ])->find();

                if (empty($Incomelog)) {

                    Agent::where('uid', $uid)->inc('income', $income)->update();
                    Agent::where('uid', $uid)->inc('total_income', $income)->update();

                    $incomedata['uid'] = $uid;
                    $incomedata['ptype'] = 1;
                    $incomedata['weid'] = $order_info['weid'];
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
    }
}
