<?php

namespace app\model;

use think\Model;

class MemberAuthGroup extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'member_auth_group';

	public static function addmemberauthgroup()
	{
		$data["weid"] = weid();
		$data["title"] = '普通会员';
		$data["is_lookprice"] = 1;
		$data["is_buyright"] = 1;
		$data["description"] = '普通会员';
		$data["upgrademoney"] = 0;
		$data["status"] = '1';

		self::create($data);
	}

	public static function getdefaultGroup()
	{
		$MemberAuthGroup = MemberAuthGroup::where(['weid' => weid()])
			->order('is_default desc id asc')
			->find();

		if (!empty($MemberAuthGroup)) {
			$MemberAuthGroup = $MemberAuthGroup->toArray();
		}
		return $MemberAuthGroup;
	}

	public static function getGroup($id = '')
	{
		$list = self::where(['weid' => weid()])->select()->toArray();
		foreach ($list as $v) {
			$data[$v['id']] = $v;
		}
		if (empty($id)) {
			return $data;
		} else {
			return $data[$id];
		}
	}
	public static function getGroupinfo($id)
	{
		$data = self::find($id);
		if (!empty($data)) {
			return $data->toArray();
		}
	}

	public static function getPoints($id, $ordertotal)
	{
		$data = self::find($id);
		$points = 0;
		if (!empty($data)) {

			$data = $data->toArray();

			if ($data['points'] > 0) {

				if ($data['points_method'] == 0) {
					$points = ($ordertotal * percent_to_num($data['points']));
				} elseif ($data['points_method'] == 1) {
					$points = $data['points'];
				}
			}
		}
		return $points;
	}

	public static function getarray()
	{
		$data = self::where(['weid' => weid()])->order('id desc')->select()->toArray();

		if (empty($data)) {
			self::addmemberauthgroup();
			$data = self::where(['weid' => weid()])->order('id desc')->select()->toArray();
		}

		if (!empty($data)) {
			foreach ($data as $vo) {
				$datalist[$vo['id']] = $vo['title'];
			}
		}

		return $datalist;
	}
	public static function getpcarray($is_upgrademoney = 0)
	{
		$data = self::field('id,title,upgrademoney')->where(['weid' => weid()])->select()->toArray();

		if (empty($data)) {
			self::addmemberauthgroup();
			$data = self::where(['weid' => weid()])->order('id desc')->select()->toArray();
		}

		$datalist = [];
		foreach ($data as $key => $vo) {
			$datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['title'];
			if ($is_upgrademoney) {
				$datalist[$key]['key'] = $datalist[$key]['key'] . $vo['upgrademoney'] . '元';
			}
		}
		return $datalist;
	}
	public static function getgroupName($id)
	{
		$ret = self::find($id);
		if (!empty($ret)) {
			$ret = $ret->toArray();
		}

		return $ret['title'];
	}
}
