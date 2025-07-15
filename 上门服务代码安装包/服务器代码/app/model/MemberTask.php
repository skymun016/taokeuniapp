<?php

namespace app\model;

use think\Model;

class MemberTask extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'member_task';


	public static function getinfo($id)
	{
		$data = self::find($id);
		if (!empty($data)) {
			return $data->toArray();
		}
	}


	public static function getTitle($id)
	{
		$ret = self::find($id);
		if (!empty($ret)) {
			$ret = $ret->toArray();
		}
		return $ret['title'];
	}
}
