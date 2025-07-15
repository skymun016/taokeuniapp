<?php

namespace app\model;

use think\Model;

class OperatingcityType extends Model
{
	public static function getpcarray()
	{
		$datalist[0]['val'] = 1;
		$datalist[0]['key'] = '省代';
		$datalist[1]['val'] = 2;
		$datalist[1]['key'] = '市代';
		$datalist[2]['val'] = 3;
		$datalist[2]['key'] = '县/区代';
		return $datalist;
	}

	public static function getTitle($val)
	{
		$datalist = self::getpcarray();
		foreach ($datalist as $vo) {
			$res[$vo['val']] = $vo['key'];
		}
		return $res[$val];
	}
}
