<?php

namespace app\model\kefu;

use think\Model;

class Seating extends Model
{


	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'kefu_seating';

	public static function getlistbygroups()
	{
		$weid = weid();

		$groupswhere['weid'] = $weid;
		$gid = (int) input('gid', '', 'serach_in');
		if (!empty($gid)) {
			$groupswhere['id'] = $gid;
		}

		$retarray = Seatinggroups::where($groupswhere)
			->field('id,title')
			->order('px ASC')
			->select()
			->toArray();

		

		foreach ($retarray as $kk => $vv) {
			if (!empty($vv['id'])) {

				$uerslist = self::where(['weid' => $weid, 'groupid' => $vv['id'], 'status' => '1'])
					->where('week', 'find in set', date("w"))
					->order('px ASC')
					->select()
					->toArray();

				$uerslist_data = array(); //重新组合符合当前服务时间的客服数组
				foreach ($uerslist as $key => $value) {
					$w_a = $value['week'];
					$now_time = date('H:i');;

					if ($now_time >= $value['begintime'] && $now_time <= $value['endtime']) {
						array_push($uerslist_data, $value);
					}
				}
				if (!empty($uerslist_data)) {
					$retarray[$kk]['seating'] = $uerslist_data;
				}else{
					unset($retarray[$kk]);
				}
			}
		}
		return $retarray;
	}
}
