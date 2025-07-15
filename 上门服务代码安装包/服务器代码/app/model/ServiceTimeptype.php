<?php

namespace app\model;

use think\Model;

class ServiceTimeptype extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'service_time_ptype';

	public static function getTitle($id = '')
	{
		if (empty($id)) {
			return '默认';
		}
		$data = self::where(['id' => $id])->find();
		if (!empty($data)) {
			$data = $data->toArray();
		}
		return $data['title'];
	}


	public static function getpcarray()
	{
		$data = self::field('id,title')->where(['weid' => weid()])->where('status', 1)->select()->toArray();
		$datalist = [];
		foreach ($data as $key => $vo) {
			$datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['title'];
		}
		return $datalist;
	}
}
