<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\AgentLevel;

class AgentlevelController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = AgentLevel::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$datalist = $query->order('grade asc,id asc')->select()->toArray();

		if (empty($datalist)) {
			AgentLevel::datainitial();
			$datalist = $query->order('grade asc,id asc')->select()->toArray();
		}

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,is_default,is_teamaward,grade');
		if (!$data['id']) throw new ValidateException('参数错误');
		AgentLevel::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);
		$weid = weid();

		if (empty($data['grade'])) throw new ValidateException('等级不能为空');

		if (empty($id)) {

			if (AgentLevel::where('weid', $weid)->where('grade', $data['grade'])->find()) {
				throw new ValidateException('当前等级已经存在');
			}
			$data['weid'] = $weid;
			try {
				$res = AgentLevel::create($data);
				if ($res->id && empty($data['sort'])) {
					AgentLevel::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			if (AgentLevel::where('weid', $weid)->where('grade', $data['grade'])->where('id', '<>', $data['id'])->find()) {
				throw new ValidateException('当前等级已经存在');
			}
			try {
				AgentLevel::update($data);
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
		$data = AgentLevel::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new AgentLevel());
	}
}
