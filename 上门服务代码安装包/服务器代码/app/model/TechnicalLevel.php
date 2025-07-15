<?php

namespace app\model;

use think\Model;

class TechnicalLevel extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'technical_level';

	public static function getTitle($id = '')
	{
		$data = self::where(['id' => $id])->find();
		if (!empty($data)) {
			$data = $data->toArray();
		}
		return $data['title'];
	}
	public static function getPercent($id = '')
	{
		$data = self::where(['id' => $id])->find();
		if (!empty($data)) {
			$data = $data->toArray();
		}
		return $data['return_percent'];
	}

	public static function getpcarray()
	{
		$data = self::field('id,title')->where(['weid' => weid()])->select()->toArray();
		$datalist = [];
		foreach ($data as $key => $vo) {
			$datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['title'];
		}
		return $datalist;
	}

	public static function getarray()
	{
		$data = self::where(['weid' => weid()])->order('id asc')->select()->toArray();
		if (!empty($data))
			foreach ($data as $vo) {
				$datalist[$vo['id']] = $vo['title'];
			}
		else
			$datalist['0'] = '可添加师傅等级选择';

		return $datalist;
	}

	public static function datainitial()
	{
		$weid = weid();
		$datalist = self::where(['weid' => $weid])->select()->toArray();

		if (empty($datalist)) {
			self::create([
				'weid' => $weid,
				'title' => '一级师傅',
				'status' => 1
			]);
		}
	}
}
