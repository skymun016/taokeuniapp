<?php

namespace app\model\kefu;

use think\Model;

class Seatinggroups extends Model
{


	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'kefu_seatinggroups';

	public static function getallarray()
	{
		$list =	self::field('id,title')
			->order('id asc')
			->select()
			->toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
		}
		return $array;
	}
}
