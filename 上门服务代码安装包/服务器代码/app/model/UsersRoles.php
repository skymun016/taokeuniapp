<?php

namespace app\model;

use think\Model;

class UsersRoles extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'users_roles';

	public static function getsonid($id)
	{
		$data = self::where(['weid' => weid(), 'pid' => $id])
			->select()->toArray();
		$returndata = '';
		if (!empty($data)) {
			foreach ($data as $vo) {
				if ($returndata) {
					$returndata = $returndata . "," . $vo['id'];
				} else {
					$returndata =  $vo['id'];
				}
			}
		}
		if (!empty($returndata)) {
			return explode(',', $returndata);
		}
	}

	public static function getadminids()
	{
		$data = self::where(['weid' => weid(), 'sid' => 0, 'ocid' => 0])
			->field('id,title')->select()->toArray();
		$returndata = '';
		if (!empty($data)) {
			foreach ($data as $vo) {
				if ($returndata) {
					$returndata = $returndata . "," . $vo['id'];
				} else {
					$returndata =  $vo['id'];
				}
			}
		}
		if (!empty($returndata)) {
			return explode(',', $returndata);
		}
	}

	public static function getallarray($where = [])
	{
		$query = self::where([
			'weid' => weid(),
			'ocid' => (int) $where['ocid'],
			'tzid' => (int) $where['tzid'],
			'sid' => (int) $where['sid']
		])->field('id,title')->order('id asc');
		$list =	$query->select()->toArray();
		if (empty($list)) {
			UsersRoles::datainitial();
			$list = $list =	$query->select()->toArray();
		}
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
		}
		return $array;
	}

	public static function getpcarray($where = [])
	{
		$data = self::field('id,pid,title')->where([
			'weid' => weid(),
			'ocid' => (int) $where['ocid'],
			'tzid' => (int) $where['tzid'],
			'sid' => (int) $where['sid']
		])->order('id asc')->select()->toArray();

		$datalist = [];
		foreach ($data as $key => $vo) {
			$datalist[$key]['val'] = $vo['id'];
			$datalist[$key]['key'] = $vo['title'];
			$datalist[$key]['pid'] = $vo['pid'];
		}
		return $datalist;
	}

	public static function getPid($id)
	{
		$data = self::find($id);
		if (!empty($data)) {
			return $data->pid;
		}
	}

	public static function getdatarules($role_id)
	{
		$UsersRoles = self::find($role_id);
		if (!empty($UsersRoles)) {
			return $UsersRoles->datarules;
		}
	}

	public static function getinfo($user)
	{
		$UsersRoles = [];
		if ($user['role_id'] == 0) {
			if ($user['username'] == 'admin') {
				$UsersRoles['title'] = '创始人';
				$UsersRoles['is_console'] = 1;
				$UsersRoles['is_allrole'] = 1;
				$UsersRoles['access'] = 'all';
				$UsersRoles['status'] = 1;
			} elseif (!empty($user['sid'])) {
				//店铺创始人
				$storeusers = Users::where(['sid' => $user['sid']])->order('id asc')->find();
				if ($storeusers->uuid == $user['uuid']) {
					$UsersRoles['title'] = '店铺创始人';
					$UsersRoles['is_allrole'] = 1;
					$UsersRoles['access'] = 'all';
					$UsersRoles['status'] = 1;
				}
			} elseif (!empty($user['ocid'])) {
				//城市代理创始人
				$storeusers = Users::where(['ocid' => $user['ocid']])->order('id asc')->find();
				if ($storeusers->uuid == $user['uuid']) {
					$UsersRoles['title'] = '城市代理创始人';
					$UsersRoles['is_allrole'] = 1;
					$UsersRoles['access'] = 'all';
					$UsersRoles['status'] = 1;
				}
			} elseif (!empty($user['tzid'])) {
				//社区
				$storeusers = Users::where(['tzid' => $user['tzid']])->order('id asc')->find();
				if ($storeusers->uuid == $user['uuid']) {
					$UsersRoles['title'] = '社区创始人';
					$UsersRoles['is_allrole'] = 1;
					$UsersRoles['access'] = 'all';
					$UsersRoles['status'] = 1;
				}
			} else {
				$group_access = \think\facade\Db::name('auth_group_access')->where('uid', $user['id'])->find();
				if (!empty($group_access)) {
					$group = \think\facade\Db::name('auth_group')->where('id', $group_access['group_id'])->find();

					if (!empty($group)) {

						$UsersRoles = self::where(['title' => $group['name']])->find();
						if (!empty($UsersRoles)) {
							Users::update(['id' => $user['id'], 'role_id' => $UsersRoles['id']]);
						}
					}
				}
			}
		} else {
			$UsersRoles = self::find($user['role_id']);

			if (!empty($UsersRoles)) {
				$UsersRoles = $UsersRoles->toArray();
				if ($UsersRoles['weid'] > 0) {
					$UsersRoles['is_console'] = 0;
				}

				if (weid() > 0 && $UsersRoles['is_console'] == 1) {
					$UsersRoles['access'] = 'all';
				}
				if ($UsersRoles['is_allrole'] == 1) {
					$UsersRoles['access'] = 'all';
				}
			}
		}
		return $UsersRoles;
	}

	public static function datainitial()
	{
		$weid = weid();
		self::create([
			'weid' => $weid,
			'title' => '超级管理员',
			'access' => 'all',
			'status' => 1
		]);
	}
}
