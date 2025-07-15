<?php

namespace app\model;

use think\Model;

class PlatformScheme extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'platform_scheme';

	public static function getpcarray($where = [])
	{
		$data = self::field('id,title')->order('id asc')->select()->toArray();

		$datalist = [];
		foreach ($data as $key => $vo) {
			$datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['title'];
		}
		return $datalist;
	}
}
