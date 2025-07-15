<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Incomelog;
use app\model\Member;
use app\model\Order;
use app\model\Agent;
use app\model\AgentLevel;
use app\model\RegisterField;

class AgentController extends Base
{
	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$ptype = 'agent';

		$Fielddata = RegisterField::where(['weid' => $weid, 'ptype' => $ptype])->select()->toArray();

		if (empty($Fielddata)) {
			RegisterField::datainitial($ptype);
		}

		$query = $this->setSearch();

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		if (!empty($res['data'])) {
			foreach ($res['data'] as &$vo) {
				$vo = Agent::conversion($vo);
			}
		}
		$data['data'] = $res;
		if ($page == 1) {
			$data['field_data']['RegisterField'] = RegisterField::getlistViewField($ptype);
		}

		return $this->json($data);
	}
	function setSearch()
	{
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = input('post.status', '', 'serach_in');
		$create_time = input('post.create_time', '', 'serach_in');
		$path = input('post.path', '', 'serach_in');
		$weid = weid();

		if ($path == "/agent/audit") {
			$status = "0";
		}

		$query = Agent::where(['weid' => $weid]);

		if (!empty($create_time)) {
			$query->where('create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]);
		}

		if (!empty($keyword)) {
			$query->where('title|tel', 'like', '%' . $keyword . '%');
		}

		if (!empty($status) || $status === "0") {
			$query->where(['status' => $status]);
		}
		return $query;
	}
	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Agent::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	function delete()
	{
		return $this->del(new Agent());
	}
	function subordinates()
	{
		$weid = weid();
		$uid = input('post.uid', '', 'serach_in');
		$keyword = input('post.keyword', '', 'serach_in');
		if (empty($uid)) {
			$uid = -1;
		}
		$query = Member::where(['weid' => $weid, 'pid' => $uid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id asc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['is_agent'] = yesno(Agent::is_agent($vo['id']));
			$vo['subordinatescount'] = Member::where(['pid' => $vo['id']])->count();
			$vo['ordercount'] = Incomelog::where(['weid' => $weid, 'uid' => $vo['id']])->count();
		}

		$data['data'] = $res;

		return $this->json($data);
	}
	//导出
	function dumpdata()
	{
		$page = $this->request->post('page', 1, 'intval');
		$ptype = 'agent';
		$query = $this->setSearch();
		$data = RegisterField::dumpdata($query, $ptype, $page);
		return $this->json($data);
	}

	public function agentorder()
	{
		$weid = weid();
		$uid = input('post.uid', '', 'serach_in');
		$keyword = input('post.keyword', '', 'serach_in');
		if (empty($uid)) {
			$uid = -1;
		}

		$where['weid'] = $weid;
		$where['uid'] = $uid;

		$query = Incomelog::where($where);

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['username'] = Member::get_name($vo['buyer_id']);
			$vo['pay_time'] = time_format($vo['pay_time']);
		}
		$data['data'] = $res;

		return $this->json($data);
	}
}
