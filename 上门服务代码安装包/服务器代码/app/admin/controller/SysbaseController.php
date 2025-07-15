<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Files;
use app\model\Users;
use app\model\AdminMenu;
use app\model\UsersRoles;

class SysbaseController extends Base
{
	/*
 	* @Description 图片管理列表
 	*/
	function fileList()
	{
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];

		$field = 'id,filepath,hash,create_time';

		$res = Files::where($where)->field($field)->order('id desc')->paginate(['list_rows' => $limit, 'page' => $page])->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	/*
 	* @Description  删除图片
 	*/
	function deleteFile()
	{
		$filepath =  $this->request->post('filepath', '', 'serach_in');
		if (!$filepath) $this->error('请选择图片');

		Files::where('filepath', 'in', $filepath)->delete();

		return $this->json(['msg' => '操作成功']);
	}
	public function sysversion()
	{
		if (config('database.app_name') == config('my.app_v2')) {
			return $this->json(['data' => 'v2']);
		} elseif (config('database.app_name') == config('my.app_v3')) {
			return $this->json(['data' => 'v3']);
		} elseif (config('database.app_name') == config('my.app_v6')) {
			return $this->json(['data' => 'v6']);
		}
	}

	/*
 	* @Description  重置密码
 	*/
	public function resetPwd()
	{
		$password = $this->request->post('password');

		if (empty($password)) $this->error('密码不能为空');

		$data['id'] = $this->userInfo['id'];

		$data["salt"] = substr(md5(uniqid()), 8, 8);

		$data['password'] = pass_hash($password, $data["salt"]);

		$res = Users::update($data);

		return $this->json(['msg' => '操作成功']);
	}


	/*
 	* @Description  清除缓存
 	*/
	public function clearCache()
	{
		$appname = app('http')->getName();
		try {
			deldir(app()->getRootPath() . 'runtime/cache');
		} catch (\Exception $e) {
			abort(config('my.error_log_code'), $e->getMessage());
		}
		return $this->json(['msg' => '操作成功']);
	}

	public function getParentsMenus()
	{
		$field = 'id,title,path,status,icon,sort';

		$query = AdminMenu::field($field)->where(['pid' => 0, 'status' => 1]);		

		$query->where('is_admin', 1);

		if (config('database.app_name') == config('my.app_v2')) {
			$query->where('is_v2', 1);
		}

		if (config('database.app_name') == config('my.app_v3')) {
			$query->where('is_v3', 1);
		}
		if (config('database.app_name') == config('my.app_v6')) {
			$query->where('is_v6', 1);
		}

		$menus = $query->order('sort asc')->select()->toArray();

		return $this->json(['menus' => $menus]);
	}

	public function getRolesMenus()
	{
		$menu = $this->getAdminMenus(0);

		$order_array = array_column($menu, 'sort');	 //数组排序 根据
		array_multisort($order_array, SORT_ASC, $menu);

		return $this->json(['menus' => $menu]);
	}
	


	//权限系统获取菜单
	private function getAdminMenus($pid)
	{
		$field = 'id,title,path,status,icon,sort';
		$rolesInfo = UsersRoles::getinfo($this->userInfo);

		$query = AdminMenu::field($field)->where(['pid' => $pid, 'status' => 1]);
		if ($rolesInfo['is_allrole'] != 1 && $rolesInfo['pid'] != 0) {
			$query->where('path', 'in', $rolesInfo['access']);
		}

		if (!empty($this->sid)) {
			$query->where('is_store', 1);
		} elseif (!empty($this->cashregister)) {
			$query->where('is_cashregister', 1);
		} elseif (!empty($this->ocid)) {
			$query->where('is_city', 1);
		} elseif (!empty($this->tzid)) {
			$query->where('is_tuanzhang', 1);
		} elseif ($this->console == 1) {
			$query->where('is_console', 1);
		} else {
			$query->where('is_admin', 1);
		}

		if (config('database.app_name') == config('my.app_v2')) {
			$query->where('is_v2', 1);
		}

		if (config('database.app_name') == config('my.app_v3')) {
			$query->where('is_v3', 1);
		}
		if (config('database.app_name') == config('my.app_v6')) {
			$query->where('is_v6', 1);
		}

		$list = $query->order('sort asc')->select()->toArray();

		if ($list) {
			foreach ($list as $key => $val) {
				$menus[$key]['sort'] = $val['sort'];
				$menus[$key]['access'] = $val['path'];
				$menus[$key]['title'] = $val['title'] . '' . '(' . $val['path'] . ')';

				$sublist = AdminMenu::field($field)
					->where(['pid' => $val['id'], 'status' => 1])
					->order('sort asc')
					->select()
					->toArray();
				if ($sublist) {
					$menus[$key]['children'] = $this->getAdminMenus($val['id']);
				}
			}
			if (is_array($menus)) {
				$menus = array_values($menus);
			}
			return $menus;
		}
	}
}
