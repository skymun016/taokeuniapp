<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Order;
use app\model\Incomelog;
use app\model\TechnicalIncomelog;
use app\model\StoreIncomelog;
use app\model\OperatingcityIncomelog;


class StatisticalController extends Base
{

    function index()
    {
        $weid = weid();
        $create_time = input('post.create_time', '', 'serach_in');
        $where[] = ['weid', '=', $weid];

        if (!empty($create_time)) {
            $where[] = ['create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]];
        } else {
            $interval = input('post.interval', '', 'serach_in');
            $create_time = [];
            if ($interval == '日') {
                $create_time[0] = date("Y-m-d", strtotime("-1 day"));
                $create_time[1] = date("Y-m-d", time());
                $where[] = ['create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]];
            } elseif ($interval == '周') {
                $create_time[0] = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));
                $create_time[1] = date("Y-m-d", time());
                $where[] = ['create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]];
            } elseif ($interval == '月') {
                $create_time[0] = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
                $create_time[1] = date("Y-m-d", time());
                $where[] = ['create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]];
            } elseif ($interval == '年') {
                $create_time[0] = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
                $create_time[1] = date("Y-m-d", time());
                $where[] = ['create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]];
            }
        }

        $list[0]['ptypename'] = '服务';
        $list[1]['ptypename'] = '次卡';
        $list[2]['ptypename'] = '商品';

        foreach ($list as &$vo) {
            if ($vo['ptypename'] == '服务') {

                $whereService = $where;
                $whereService['ptype'] = 2;

                $vo['ordercount'] = Order::where($whereService)->count();
                $vo['total'] = Order::where($whereService)->where('pay_time', '>', 0)->sum('total');
                $vo['deposit'] = Order::where($whereService)->where('is_additional', 1)->sum('total');
                $vo['additional'] = Order::where($whereService)->where('is_additional', 1)->sum('additional');

                $vo['AgentIncome'] = Incomelog::where('order_id', 'IN', function ($query) use ($whereService) {
                    $query->name('order')->where($whereService)->field('id');
                })
                    ->sum('income');
                $vo['TechnicalIncome'] = TechnicalIncomelog::where('order_id', 'IN', function ($query) use ($whereService) {
                    $query->name('order')->where($whereService)->field('id');
                })
                    ->sum('income');
                $vo['StoreIncome'] = StoreIncomelog::where('order_id', 'IN', function ($query) use ($whereService) {
                    $query->name('order')->where($whereService)->field('id');
                })
                    ->sum('income');
                $vo['OperatingcityIncome'] = OperatingcityIncomelog::where('order_id', 'IN', function ($query) use ($whereService) {
                    $query->name('order')->where($whereService)->field('id');
                })
                    ->sum('income');
            } elseif ($vo['ptypename'] == '次卡') {
                $whereTimes = $where;
                $whereTimes['ptype'] = 2;
                $whereTimes['is_times'] = 1;

                $vo['ordercount'] = Order::where($whereTimes)->count();
                $vo['total'] = Order::where($whereTimes)->where('pay_time', '>', 0)->sum('total');
                $vo['deposit'] = '-';
                $vo['additional'] = '-';
                $vo['AgentIncome'] = Incomelog::where('order_id', 'IN', function ($query) use ($whereTimes) {
                    $query->name('order')->where($whereTimes)->field('id');
                })
                    ->sum('income');
                $vo['TechnicalIncome'] = TechnicalIncomelog::where('order_id', 'IN', function ($query) use ($whereTimes) {
                    $query->name('order')->where($whereTimes)->field('id');
                })
                    ->sum('income');
                $vo['StoreIncome'] = StoreIncomelog::where('order_id', 'IN', function ($query) use ($whereTimes) {
                    $query->name('order')->where($whereTimes)->field('id');
                })
                    ->sum('income');
                $vo['OperatingcityIncome'] = OperatingcityIncomelog::where('order_id', 'IN', function ($query) use ($whereTimes) {
                    $query->name('order')->where($whereTimes)->field('id');
                })
                    ->sum('income');
            } elseif ($vo['ptypename'] == '商品') {

                $whereGoods = $where;
                $whereGoods['ptype'] = 1;

                $vo['ordercount'] = Order::where($whereGoods)->count();
                $vo['total'] = Order::where($whereGoods)->where('pay_time', '>', 0)->sum('total');
                $vo['deposit'] = '-';
                $vo['additional'] = '-';
                $vo['TechnicalIncome'] = '-';
                $vo['AgentIncome'] = Incomelog::where('order_id', 'IN', function ($query) use ($whereGoods) {
                    $query->name('order')->where($whereGoods)->field('id');
                })
                    ->sum('income');
                $vo['StoreIncome'] = StoreIncomelog::where('order_id', 'IN', function ($query) use ($whereGoods) {
                    $query->name('order')->where($whereGoods)->field('id');
                })
                    ->sum('income');
                $vo['OperatingcityIncome'] = OperatingcityIncomelog::where('order_id', 'IN', function ($query) use ($whereGoods) {
                    $query->name('order')->where($whereGoods)->field('id');
                })
                    ->sum('income');
            }
            $vo['platformIncome'] = (int)$vo['total'] - (int)$vo['AgentIncome'] - (int)$vo['TechnicalIncome'] - (int)$vo['StoreIncome'] - (int)$vo['OperatingcityIncome'];
            $vo['starttime'] = $create_time[0];
            $vo['endtime'] = $create_time[1];
        }

        $data['data'] = $list;


        return $this->json($data);
    }
}
