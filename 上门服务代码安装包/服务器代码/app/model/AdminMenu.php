<?php

namespace app\model;

use think\Model;

class AdminMenu extends Model
{


	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'sys_admin_menu';


	public static function getpidarray()
	{
		$list =	self::field('id,title,pid')
			->order('sort asc')
			->select()
			->toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
			$array[$k]['pid'] = $v['pid'];
		}
		return $array;
	}
	public static function getsonid($id)
	{
		//加空判定
		if (empty($id)) {
			return '';
		}
		$data = self::where(['pid' => $id])
			->select()->toArray();
		$returndata = $id;
		if (!empty($data)) {
			foreach ($data as $vo) {
				if ($son = self::getsonid($vo['id'])) {
					$returndata = $returndata . "," .$son;
				} else {
					$returndata = $returndata . "," . $vo['id'];
				}
			}
		}
		return $returndata;
	}

	public static function getidssonid($ids)
	{

		if (!empty($ids)) {
			$returndata = '';
			$idsarray =	explode(',', $ids);
			foreach ($idsarray as $vo) {
				$sonid = '';
				$sonid = self::getsonid($vo);
				if (empty($returndata)) {
					if (!empty($sonid)) {
						$returndata = $sonid;
					}
				} else {
					if (!empty($sonid)) {
						$returndata = $returndata . "," . $sonid;
					}
				}
			}
		}

		return $returndata;
	}
}
