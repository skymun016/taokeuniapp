<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\UsersRoles;

class UsersrolesController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = trim(input('post.status', '', 'serach_in'));

		$where = [];
		$where['weid'] = $weid;
		$where['sid'] = (int) $this->sid;
		$where['ocid'] = (int) $this->ocid;
		$where['tzid'] = (int) $this->tzid;
		if ($status !== '') {
			$where['status'] = $status;
		}

		$field = 'id,pid,title,status,description';
		$query = UsersRoles::where($where);

		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		//UsersRoles::getsonid($this->userInfo['role_id']);

		$query->field($field)->order('id asc');

		$datalist = $query->select()->toArray();

		if (empty($datalist) && empty($this->sid) && empty($this->ocid) && empty($this->tzid)) {
			UsersRoles::datainitial();
			$datalist = $query->select()->toArray();
		}
		$pid = $this->userInfo['role_id'];
		if (!empty($pid)) {
			$tmppid = UsersRoles::getPid($pid);
			if ($tmppid == 0) {
				$pid = 0;
			}
		}
		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		UsersRoles::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$weid = weid();
		$id = $this->request->post('id');
		$data = input('post.');

		if (!in_array('Home', $data['access'])) {
			array_push($data['access'], 'Home');
		}
		if (!empty($data['access'])) {
			$data['access'] = implode(',', $data['access']);
		}

		if ($weid == 0) {
			$data['is_console'] = 1;
		} else {
			$data['is_console'] = 0;
		}

		if (empty($id)) {
			$data['weid'] = (int) $weid;
			$data['sid'] = (int) $this->sid;
			$data['ocid'] = (int) $this->ocid;
			$data['tzid'] = (int) $this->tzid;
			$res = UsersRoles::create($data);
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			UsersRoles::update($data);
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) $this->error('参数错误');
		$res = UsersRoles::find($id);
		if (!empty($res)) {
			$res = $res->toArray();
		}
		if(!empty($res['scope'])) {
			$res['scope'] = explode(',', $res['scope']);
		}
		if(!empty($res['access'])) {
			$res['access'] = explode(',', $res['access']);
		}

		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new UsersRoles());
	}
	function getField()
	{
		$data['pids'] = UsersRoles::getpcarray(['ocid' => $this->ocid, 'sid' => $this->sid, 'tzid' => $this->tzid]);

		return $this->json(['data' => $data]);
	}
}
