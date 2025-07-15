<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use think\facade\Db;
use app\model\OrderGoods;
use app\model\OrderTimescard;

class TuanzhangincomelogController extends Base
{

	/*
 	* @Description  数据列表
 	*/
	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$create_time = input('post.create_time', '', 'serach_in');

		$where = [];
		$where['weid'] = weid();
		$query = Db::name('tuanzhang_incomelog')
			->alias('ti')
			->join('order', 'ti.order_id = order.id')
			->join('member', 'ti.buyer_id = member.id')
			->join('tuanzhang', 'ti.uuid = tuanzhang.uuid')
			->leftJoin('order_timescard', 'ti.order_id = order_timescard.order_id')
			->field('ti.*, order.begin_time, order.end_time
			,order.is_times,member.nickname,tuanzhang.title,order_timescard.timestype');

		if (!empty($keyword)) {
			$query->where('ti.order_num_alias|member.nickname|tuanzhang.title', 'like', '%' . $keyword . '%');
		}

		if (!empty($create_time)) {
			$query->where('ti.create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]);
		}
		$res = $query->order('ti.id desc')->paginate(getpage())->toArray();

		foreach ($res['data'] as &$vo) {
			if ($vo['is_times'] == 1) {

				$OrderTimescard = OrderTimescard::where('order_id', $vo['id'])->order('id asc')->select()->toArray();
                if ($OrderTimescard) {
                    foreach ($OrderTimescard as $tcvo) {
                        if ($tcvo['yue_date']) {
                            if ($tcvo['timestype'] == 1) {
                                if ($vo['yue_time']) {
                                    $vo['yue_time'] .= ';每月:' . $tcvo['yue_date'] . '号';
                                } else {
                                    $vo['yue_time'] = '每月:' . $tcvo['yue_date'] . '号';
                                }
                            } else {
                                if ($vo['yue_time']) {
                                    $vo['yue_time'] .= ';每周周:' . $tcvo['yue_date'];
                                } else {
                                    $vo['yue_time'] = '每周周:' . $tcvo['yue_date'];
                                }
                            }
                        }
                    }
                }

                if (empty($vo['yue_time'])) {
                    $vo['yue_time'] = '还没有预约时间';
                }
			} else {
				$vo['yue_time'] = time_format($vo['begin_time']) . ' 到 ' . date('H:i', $vo['end_time']);
			}

			$vo['orderGoods'] = OrderGoods::where('order_id', $vo['order_id'])->select();
		}

		$data['data'] = $res;
		return $this->json($data);
	}
}
