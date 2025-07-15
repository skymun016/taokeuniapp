<?php
namespace app\model;

use think\Model;

class Coupon extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'coupon';
    public static function getpcarray($ptype = '')
	{
		$where['weid'] = weid();
		$where['status'] = 1;
		if (!empty($ptype)) {
			$where['ptype'] = $ptype;
		}

		$list =	self::field('id,name')->where($where)
			->order('sort asc')
			->select()
			->toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['name'];
		}
		return $array;
	}
}
