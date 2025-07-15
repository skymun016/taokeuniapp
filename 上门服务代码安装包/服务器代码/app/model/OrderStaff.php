<?php

namespace app\model;

use think\Model;

class OrderStaff extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'order_staff';

	public static function getuuid($order_id)
	{
		$staff = self::where('order_id', $order_id)->order('id desc')->find();
		return $staff->uuid;
	}
	public static function is_staff($order_id)
	{
		$staff = self::where('order_id', $order_id)->order('id desc')->find();
		return $staff->id;
	}

	public static function ordercount($uuid)
	{
		return self::where('uuid', $uuid)->count();
	}

	public static function staff($order_id)
	{
		$staff = self::where('order_id', $order_id)->order('id desc')->find();
		return Technical::getTitle($staff->uuid);
	}
	public static function checkstaff($technicalId, $selectDate, $begin_time, $end_time)
	{
		$ret = OrderStaff::where('begin_time', '>=', strtotime($selectDate . ' ' . $begin_time . ':00'))
			->where('end_time', '<=', strtotime($selectDate . ' ' . $end_time . ':00'))
			->where('uuid', $technicalId)
			->find();
		return $ret;
	}

	public static function getTechnical($order_id)
	{
		$staff = self::where('order_id', $order_id)->order('id desc')->find();
		return Technical::getInfo($staff->uuid);
	}

	public static function addstaff($order_staff)
	{
		$order =  self::where(['order_id' => $order_staff['order_id'], 'is_complete' => 0])->select()->toArray();
		if (!empty($order)) {
			self::where(['order_id' => $order_staff['order_id'], 'is_complete' => 0])->update(['uuid' => $order_staff['uuid']]);
		} else {
			$res = self::create($order_staff);
		}

		if (!empty($res)) {
			Broadcast::staff($order_staff);
			return $res;
		}
	}
}
