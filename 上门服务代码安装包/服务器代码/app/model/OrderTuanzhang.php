<?php

namespace app\model;

use think\Model;

class OrderTuanzhang extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'order_tuanzhang';

	public static function getuuid($order_id)
	{
		$tuanzhang = self::where('order_id', $order_id)->order('id desc')->find();
		return $tuanzhang->uuid;
	}
	public static function is_tuanzhang($order_id)
	{
		$tuanzhang = self::where('order_id', $order_id)->order('id desc')->find();
		return $tuanzhang->id;
	}

	public static function ordercount($uuid)
	{
		return self::where('uuid', $uuid)->count();
	}

	public static function tuanzhang($order_id)
	{
		$tuanzhang = self::where('order_id', $order_id)->order('id desc')->find();
		return Tuanzhang::getTitlebyuuid($tuanzhang->uuid);
	}

	public static function getTuanzhang($order_id)
	{
		$tuanzhang = self::where('order_id', $order_id)->order('id desc')->find();
		return Tuanzhang::getInfo($tuanzhang->uuid);
	}

	public static function addtuanzhang($order_tuanzhang)
	{
		$order =  self::where(['order_id' => $order_tuanzhang['order_id'], 'is_complete' => 0])->select()->toArray();
		if (!empty($order)) {
			self::where(['order_id' => $order_tuanzhang['order_id'], 'is_complete' => 0])->update(['uuid' => $order_tuanzhang['uuid']]);
		} else {
			$res = self::create($order_tuanzhang);
		}
	}
}
