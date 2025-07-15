<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Users;
use app\model\UsersSessions;
use app\model\UsersRoles;
use app\model\Department;
use app\model\Store;
use app\model\Operatingcity;
use app\model\Tuanzhang;
use app\model\Config;
use think\console\command\make\Model;
use think\db\Where;

class UsersController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$sex = input('post.sex', '', 'serach_in');
		$status = input('post.status', '', 'serach_in');
		$role_id = input('post.role_id', '', 'serach_in');

		$where = [];

		$where['users.weid'] = (int) $weid;

		$where['users.sid'] = (int) $this->sid;

		$where['users.ocid'] = (int) $this->ocid;

		$where['users.tzid'] = (int) $this->tzid;

		if (!empty($sex)) {
			$where['users.sex'] = $sex;
		}
		if ($status !== '') {
			$where['users.status'] = $status;
		}

		if (!empty($role_id)) {
			$where['users.role_id'] = $role_id;
		}

		$field = 'id,title,username,sex,touxiang,remark,status,create_time';

		$withJoin = [
			'roles' => explode(',', 'title'),
		];

		$query = Users::where($where)->withJoin($withJoin, 'left');

		$query->where('roles.sid', (int) $this->sid);
		$query->where('roles.ocid', (int) $this->ocid);
		$query->where('roles.tzid', (int) $this->tzid);

		if (!empty($keyword)) {
			$query->where('users.title|users.username', 'like', '%' . $keyword . '%');
		}

		$query->where('users.id', '<>', $this->userInfo['id']);

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();
		$data['data'] = $res;

		if ($page == 1) {
			$data['field_data']['role_ids'] = UsersRoles::getallarray();
		}
		return $this->json($data);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$usersdata = only('title,username,password,sex,touxiang,role_id,remark,status,create_time');
		$usersdata['username'] = trim($usersdata['username']);
		if ($usersdata['username']) {
			$chackuser = Users::where('username', $usersdata['username']);
			if (!empty($id)) {
				$chackuser->Where('id', '<>', $id);
			}
			$chackuser = $chackuser->find();
			if ($chackuser) {
				throw new ValidateException('用户名已被占用');
			}
		}

		if (!empty($usersdata['password'])) {
			$usersdata["salt"] = substr(md5(uniqid()), 8, 8);
			$usersdata['password'] = pass_hash($usersdata['password'], $usersdata["salt"]);
		} else {
			unset($usersdata['password']);
		}
		unset($usersdata['create_time']);

		if (empty($id)) {
			$usersdata['weid'] = weid();
			$usersdata['uuid'] = uniqid(rand(1, 10000));
			$usersdata['w7uid'] = 0;

			if (!empty($this->sid)) {
				$usersdata['sid'] = $this->sid;
			}
			if (!empty($this->ocid)) {
				$usersdata['ocid'] = $this->ocid;
			}
			if (!empty($this->tzid)) {
				$usersdata['tzid'] = $this->tzid;
			}
			try {
				$res = Users::create($usersdata);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			$usersdata['id'] = $id;
			try {
				Users::update($usersdata);
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
		$res = Users::find($id);
		if (!empty($res)) {
			$res = $res->toArray();
			$res['password'] = '';
		}
		return $this->json(['data' => $res]);
	}

	/*
 	* @Description  修改排序开关
 	*/
	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Users::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	/*start*/
	/*
 	* @Description  删除
 	*/
	function delete()
	{
		$idx =  $this->request->post('id', '', 'serach_in');

		if (!is_array($idx)) {
			$idx = explode(',', $idx);
			if (in_array(1, $idx)) {
				throw new ValidateException('超级用户禁止删除');
			}
		}

		if ($idx == 1) {
			throw new ValidateException('超级用户禁止删除');
		}

		return $this->del(new Users());
	}

	/*
 	* @Description  禁用
 	*/
	public function forbidden()
	{
		$idx = $this->request->post('id', '', 'serach_in');
		if (empty($idx)) throw new ValidateException('参数错误');

		$data['status'] = '0';
		$res = Users::field('status')
			->where(['id' => explode(',', $idx)])
			->update($data);
		return $this->json(['msg' => '操作成功']);
	}

	//获取用户信息 菜单信息 以及菜单对应的组件
	//vue2
	public function getUserInfo()
	{
		if (!$this->userInfo['id']) {
			throw new ValidateException('用户Id不存在');
		}
		$config = Config::getconfig();

		$userInfo = Users::field('*')->where('id', $this->userInfo['id'])->find();
		if (!$userInfo) {
			throw new ValidateException('用户信息不存在');
		} else {
			$userInfo = $userInfo->toArray();
			if ($userInfo['weid'] > 0 && $userInfo['weid'] != weid()) {
				throw new ValidateException('您不是本平台管理员');
			}
		}

		$rolesInfo = UsersRoles::getinfo($userInfo);

		$menu = $this->getMyMenus($rolesInfo, $this->getTotalMenus());

		$components = $this->getComponents($menu, $userInfo['role_id']);

		if ($this->console == 1) {
			$sitesetup = Config::getconfig('sitesetup');
			$userInfo['sys_title']  = $sitesetup['sys_title'];
			$userInfo['logo'] = toimg($sitesetup['logo']);
		} else {
			if (!empty($this->userInfo['sys_title'])) {
				$userInfo['sys_title']  = $this->userInfo['sys_title'];
			} else {
				$userInfo['sys_title']  = $config['sys_title'];
			}
			$userInfo['logo'] = toimg($config['logo']);
		}

		$userInfo['w7copyright'] = $this->userInfo['w7copyright'];
		$userInfo['weid'] = weid();

		if (!empty($this->sid)) {
			$userInfo['store_title'] = Store::getTitle($this->sid);
		}

		if (!empty($this->ocid)) {
			$userInfo['city_title'] = Operatingcity::getTitle($this->ocid);
		}

		if (!empty($this->tzid)) {
			$userInfo['tuanzhang_title'] = Tuanzhang::getTitle($this->tzid);
			$userInfo['tuanzhang_sid'] = Store::getidbytzid($this->tzid);
		}

		$data['menu'] = $menu;
		$data['components'] = $components;
		if (!empty($rolesInfo['access'])) {
			$data['actions'] = explode(',', $rolesInfo['access']);
		} else {
			$data['actions'] = [];
		}
		$userInfo['is_console'] = $rolesInfo['is_console'];
		$userInfo['roles'] = $components;
		$userInfo['avatar'] = $userInfo['touxiang'];

		$data['data'] = $userInfo;

		return $this->json($data);
	}

	//获取用户信息 菜单信息 以及菜单对应的组件
	//vue3
	public function getUserInfoVue3()
	{
		if (!$this->userInfo['id']) {
			throw new ValidateException('用户Id不存在');
		}
		$config = Config::getconfig();

		$userInfo = Users::field('*')->where('id', $this->userInfo['id'])->find();
		if (!$userInfo) {
			throw new ValidateException('用户信息不存在');
		} else {
			$userInfo = $userInfo->toArray();
		}

		$rolesInfo = UsersRoles::getinfo($userInfo);

		$menu = $this->getMyMenus($rolesInfo, $this->getTotalMenus());

		$components = $this->getComponents($menu, $userInfo['role_id']);

		if ($this->console == 1) {
			$sitesetup = Config::getconfig('sitesetup');
			$data['sys_title']  = $sitesetup['sys_title'];
			$data['logo'] = toimg($sitesetup['logo']);
		} else {
			if (!empty($this->userInfo['sys_title'])) {
				$data['sys_title']  = $this->userInfo['sys_title'];
			} else {
				$data['sys_title']  = $config['sys_title'];
			}
			$data['logo'] = toimg($config['logo']);
		}

		$data['user'] = $userInfo;
		$data['w7copyright'] = $this->userInfo['w7copyright'];
		$data['weid'] = weid();

		if (!empty($this->sid)) {
			$data['store_title'] = Store::getTitle($this->sid);
		}

		if (!empty($this->ocid)) {
			$data['city_title'] = Operatingcity::getTitle($this->ocid);
		}

		if (!empty($this->tzid)) {
			$data['tuanzhang_title'] = Tuanzhang::getTitle($this->tzid);
			$data['tuanzhang_sid'] = Store::getidbytzid($this->tzid);
		}

		$data['menu'] = $menu;
		$data['components'] = $components;
		if (!empty($rolesInfo['access'])) {
			$data['permissions'] = explode(',', $rolesInfo['access']);
		} else {
			$data['permissions'] = [];
		}

		$data['is_console'] = $rolesInfo['is_console'];
		$data['roles'] = $components;
		$data['avatar'] = $data['touxiang'];

		$data['data'] = $data;

		return $this->json($data);
	}

	//获取当前角色的菜单
	private function getMyMenus($rolesInfo, $totalMenus)
	{
		if ($rolesInfo['access'] == 'all') {
			return $totalMenus;
		}
		if (!empty($rolesInfo['access'])) {
			foreach ($totalMenus as $key => $val) {
				if (in_array($val['path'], explode(',', $rolesInfo['access']))) {
					$tree[] = array_merge($val, ['children' => $this->getMyMenus($rolesInfo, $val['children'])]);
				}
			}
		}

		if (is_array($tree)) {
			$tree = array_values($tree);
		}

		return $tree;
	}

	//退出
	public function logout()
	{
		$token = $this->getToken();
		UsersSessions::where('token', $token)->delete();
		return  $this->json(['msg' => '退出成功']);
	}

	/*
 	* @Description  重置密码
 	*/
	public function resetPwd()
	{
		$data = only('id,password');
		if (empty($data['id'])) throw new ValidateException('参数错误');
		if (empty($data['password'])) throw new ValidateException('密码不能为空');

		$userdata = Users::field('id,title,create_time')->find($data['id']);
		$data["salt"] = substr(md5(uniqid()), 8, 8);

		$data['password'] = pass_hash($data['password'], $data["salt"]);

		$res = Users::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	function getField()
	{
		$data['role_ids'] = UsersRoles::getpcarray(['ocid' => $this->ocid, 'sid' => $this->sid, 'tzid' => $this->tzid]);
		return $this->json(['data' => $data]);
	}
}
