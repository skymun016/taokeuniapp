<?php

namespace app\model;

use think\Model;

class PartnerLevel extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'partner_level';

	public static function getLevel($id = '')
	{
		$partnerLevel = self::where(['id' => $id])->find();
		if (!empty($partnerLevel)) {
			$partnerLevel = $partnerLevel->toArray();
		}
		return $partnerLevel['name'];
	}

	public static function getdefault()
	{
		$partnerLevel = self::where(['weid' => weid()])
			->order('is_default desc id asc')
			->find();

		if (!empty($partnerLevel)) {
			$partnerLevel = $partnerLevel->toArray();
		}
		return $partnerLevel;
	}

	public static function getpcarray()
	{
		$data = self::field('id,name')->where(['weid' => weid()])->select()->toArray();
		$datalist = [];
		foreach ($data as $key => $vo) {
			$datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['name'];
		}
		return $datalist;
	}

	public static function getarray()
	{
		$data = self::where(['weid' => weid()])->order('id asc')->select()->toArray();
		if (!empty($data))
			foreach ($data as $vo) {
				$datalist[$vo['id']] = $vo['name'];
			}
		else
			$datalist['0'] = '可添加合伙人等级选择';

		return $datalist;
	}

	public static function datainitial()
	{
		$weid = weid();
		$datalist = self::where(['weid' => $weid])->select()->toArray();

		if (empty($datalist)) {
			self::create([
				'weid' => $weid,
				'name' => '一级合伙人',
				'type' => 1,
				'status' => 1
			]);
		}
	}
}
