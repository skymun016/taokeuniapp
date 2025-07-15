<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\AgentCode;
use app\model\Member;

class AgentcodeController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = AgentCode::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('agent_code', 'like', '%' . $keyword . '%');
		}

		$datalist = $query->order('id asc')->select()->toArray();

		foreach ($datalist as &$vo) {
			$vo['uid'] = Member::get_name($vo['uid']);
		}

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		AgentCode::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);
		if (empty($id)) {
			$data['weid'] = weid();
			try {

				$res = AgentCode::create($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			$Agentdata = AgentCode::where(['agent_code' => $data['agent_code']])->find();

			if (!empty($Agentdata)) {
				return $this->json(['code' => 2001, 'msg' => '修改失败，这个邀请码已有人用']);
			}
			try {
				AgentCode::update($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$data = AgentCode::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new AgentCode());
	}
}
