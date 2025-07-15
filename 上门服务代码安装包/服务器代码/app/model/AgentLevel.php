<?php

namespace app\model;

use think\Model;

class AgentLevel extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'agent_level';

	public static function getLevel($id = '')
	{
		$agent = self::where(['id' => $id])->find();
		if (!empty($agent)) {
			$agent = $agent->toArray();
		}
		return $agent['name'];
	}
	public static function getdefault()
	{
		$AgentLevel = AgentLevel::where(['weid' => weid()])
			->order('is_default desc id asc')
			->find();

		if (!empty($AgentLevel)) {
			$AgentLevel = $AgentLevel->toArray();
		}
		return $AgentLevel;
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
			$datalist['0'] = '可添加分销等级选择';

		return $datalist;
	}

	public static function datainitial()
	{
		$weid = weid();
		$datalist = self::where(['weid' => $weid])->select()->toArray();

		if (empty($datalist)) {
			self::create([
				'weid' => $weid,
				'name' => '一级分销商',
				'grade' => 1,
				'status' => 1
			]);
		}
	}
}
