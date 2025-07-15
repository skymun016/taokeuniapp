<?php

namespace app\model;

use think\Model;

class OrderTimescardRecord extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'order_timescard_record';

	public static function timesremain($order_id)
	{
		return self::where(['order_id' => $order_id, 'is_complete' => 0])->count();
	}
	//已使用次数
	public static function timesused($order_id)
	{
		return self::where(['order_id' => $order_id, 'is_complete' => 1])->count();
	}
	//已使用
	public static function timesusedlist($order_id)
	{
		$retdate = self::where(['order_id' => $order_id, 'is_complete' => 1])
			->where('yue_begin_time', '>', 0)
			->order('yue_begin_time asc')
			->select()
			->toArray();
		if (!empty($retdate)) {
			foreach ($retdate as &$vo) {
				$vo['yue_begin_time'] = time_format($vo['yue_begin_time']);
				$vo['yue_end_time'] = time_format($vo['yue_end_time']);
				$vo['begin_time'] = time_format($vo['begin_time']);
				$vo['end_time'] = time_format($vo['end_time']);
			}
		}
		return $retdate;
	}
}
