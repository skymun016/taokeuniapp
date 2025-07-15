<?php

namespace app\model;

use think\Model;

class AgentCode extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'agent_code';

	public static function getuid($agent_code)
	{
		$data = AgentCode::where(['agent_code' => $agent_code])->find();
		if (!empty($data)) {
			$data = $data->toArray();
			if (!empty($data['status'] == 1)) {
				return $data['uid'];
			}
		}
	}

	public static function getagent_code($uid)
	{
		$data = AgentCode::where(['uid' => $uid])->find();
		if (!empty($data)) {
			$data = $data->toArray();
			if (!empty($data['status'] == 1)) {
				return $data['agent_code'];
			} else {

				return '审核中';
			}
		} else {
			$agent_code = str_pad($uid, 6, "0", STR_PAD_LEFT);
			return self::upcode($uid, $agent_code, 1);
		}
	}
	public static function upcode($uid, $agent_code, $status)
	{
		$data = AgentCode::where(['agent_code' => $agent_code])->find();

		if (!empty($data)) {
			if ($status == 0) {
				return 0;
			} else {
				$agent_code = rand(1, 99) . time();
			}
		}

		$Codedata = AgentCode::where(['uid' => $uid])->find();

		if (empty($Codedata)) {
			self::create(['uid' => $uid, 'agent_code' => $agent_code, 'status' => $status]);
		} else {
			self::where(['uid' => $uid])->update(['agent_code' => $agent_code, 'status' => $status]);
		}
		if ($status != 1) {
			$agent_code = '审核中';
		}

		return $agent_code;
	}
}
