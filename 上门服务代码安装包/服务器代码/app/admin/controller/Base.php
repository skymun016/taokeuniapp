<?php

namespace app\admin\controller;

use think\exception\FuncNotFoundException;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\Cache;
use app\model\UsersSessions;
use app\model\Users;
use app\model\Store;
use app\model\TextReplace;
use app\model\Operatingcity;
use app\model\Tuanzhang;
use app\model\Platform;
use app\model\AdminMenu;

class Base extends \app\BaseController
{

	protected $userInfo = [];
	protected $sid = '';
	protected $ocid = '';
	protected $tzid = '';
	protected $console = '';
	protected $cashregister = '';
	protected $actionurl = '';

	protected function initialize()
	{
		$controller = $this->request->controller();
		$action = $this->request->action();
		$app = app('http')->getName();

		$url =  "{$app}/{$controller}/{$action}";
		$url = strtolower($url);
		$this->actionurl = $url;

		if (!in_array($url, array_map('strtolower', config('my.nocheck')))) {
			$this->checkTokenAuth();
		}
	}

	//设置token
	protected function setToken($data)
	{

		$weid =	$data['weid'];
		$data =	serialize($data);
		$token = md5(uniqid());

		//登录的时候把token写入数据表
		$tokenInfo = UsersSessions::where('token', $token)->find();
		if (empty($tokenInfo)) {
			UsersSessions::create([
				'weid' => $weid,
				'token' => $token,
				'ptype' => 'pc',
				'ip' => getRealIP(),
				'expire_time' => time(),
				'data' => $data,
				'status' => 1
			]);
		} else {
			UsersSessions::where('token', $token)->update([
				'weid' => $weid,
				'token' => $token,
				'ptype' => 'pc',
				'ip' => getRealIP(),
				'expire_time' => time(),
				'data' => $data,
				'status' => 1
			]);
		}

		return $token;
	}
	//设置token
	protected function getToken()
	{
		global $_W;
		$Authorization = Request::header('Authorization');
		$tokenarr = explode('|', $Authorization);
		$token = $tokenarr[0];

		if (is_array($tokenarr)) {
			foreach ($tokenarr as $vo) {
				$tmp = explode('=', $vo);
				if (is_array($tmp)) {
					$arr[$tmp[0]] = $tmp[1];
				}
			}
		}
		$this->sid =  $arr['sid'];
		$this->ocid =  $arr['ocid'];
		$this->tzid =  $arr['tzid'];
		$this->console =  $arr['console'];
		$this->cashregister =  $arr['cashregister'];

		$_W['i'] = $arr['i'];

		return $token;
	}

	//检测token
	protected function checkTokenAuth()
	{
		global $_W;
		$token = $this->getToken();
		if (!$token) {
			abort(101, 'token不能为空');
		}

		$tokenInfo = UsersSessions::where('token', $token)->find();

		if (empty($tokenInfo)) {
			abort(101, 'token不存在');
		} else {
			$tokenInfo = $tokenInfo->toArray();
		}

		if (!$tokenInfo['status']) {
			abort(101, '状态被禁用');
		}

		if (!$tokenInfo['dev_status']) {
			abort(101, '你已下线,账户在其它设备登录!');
		}

		if (($tokenInfo['expire_time'] - config('my.token_expire_time')) > time()) {
			abort(101, '登录状态已过期，请重新登录');
		}

		$this->userInfo = iunserializer($tokenInfo['data']);

		if ($this->console == 1 && (int) $this->userInfo['weid'] > 0) {
			abort(101, '你没有控制台的权限');
		}

		if (!empty($this->userInfo['sid'])) {
			$this->sid = $this->userInfo['sid'];
			$Store = Store::find($this->sid);
			if ($Store && $Store->status == 1) {
			} else {
				abort(101, '你的商铺被禁用');
			}
		}
		if (!empty($this->userInfo['ocid'])) {
			$this->ocid = $this->userInfo['ocid'];
			$Operatingcity = Operatingcity::find($this->ocid);
			if ($Operatingcity && $Operatingcity->status == 1) {
			} else {
				abort(101, '你的城市代理被禁用');
			}
		}

		if (!empty($this->userInfo['tzid'])) {
			$this->tzid = $this->userInfo['tzid'];
			$Tuanzhang = Tuanzhang::find($this->tzid);
			if ($Tuanzhang && $Tuanzhang->status == 1) {
			} else {
				abort(101, '你的帐号被禁用');
			}
		}

		if ((int) $this->userInfo['weid'] > 0) {
			$_W['uniacid'] = $this->userInfo['weid'];
		} else {
			if (!empty($_W['i'])) {
				if (!empty($this->userInfo['id'] && (int) $this->userInfo['weid'] == 0)) {
					Users::where('id', $this->userInfo['id'])->update(['lastweid' => $_W['i']]);
					$this->userInfo['lastweid'] =  $_W['i'];
				}
			}

			$_W['uniacid'] = $this->userInfo['lastweid'];
			if ($this->console == 1) {
				$_W['console'] = $this->console;
				$_W['uniacid'] = 0;
			}
		}

		if (file_exists($this->getRoot() . '/data/test.lock')) {
			if ($this->userInfo['username'] != 'admin' && in_array($this->actionurl, array_map('strtolower', config('my.testnoupdate')))) {
				throw new ValidateException('演示站不能修改核心设置！');
			}
		}

		$_W['w7copyright'] = $this->userInfo['w7copyright'];

		event('DoLog', $this->userInfo['username']); //写入操作日志
	}

	//获取所有菜单
	protected function getTotalMenus()
	{
		$menu =  $this->getBaseMenus();
		$order_array = array_column($menu, 'sort');	 //数组排序
		array_multisort($order_array, SORT_ASC, $menu);
		return $menu;
	}

	//返回当前应用的菜单列表
	protected function getBaseMenus()
	{
		$weid = weid();
		$where = [];
		$where[] = ['type', 'in', [0, 1]];
		$where[] = ['status', '=', 1];
		$query = AdminMenu::where($where);

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

		if ($this->console != 1) {
			$Schemeids = Platform::getScheme($weid);
			if($Schemeids !='all'){
				$query->where('id', 'in', AdminMenu::getidssonid($Schemeids));
			}
		}

		if (config('database.app_name') == config('my.app_v2')) {
			$query->where('is_v2', 1);
			$version = 'v2';
		}

		if (config('database.app_name') == config('my.app_v3')) {
			$query->where('is_v3', 1);
			$version = 'v3';
		}

		if (config('database.app_name') == config('my.app_v6')) {
			$query->where('is_v6', 1);
			$version = 'v6';
		}

		if (!empty($this->userInfo['w7copyright'])) {
			$query->where('w7_hidden', 0);
		}

		$list = $query->order('sort asc')->select()->toArray();
		if ($list) {
			foreach ($list as $key => $val) {

				//$menus[$key]['name'] = $val['path'];
				$menus[$key]['name'] = $val['title'];
				$menus[$key]['pid'] = $val['pid'];
				$menus[$key]['id'] = $val['id'];
				$menus[$key]['title'] = $val['title'];
				$menus[$key]['sort'] = $val['sort'];
				$menus[$key]['icon'] = $val['icon'] ? $val['icon'] : 'el-icon-menu';
				$menus[$key]['path'] = $val['path'];
				$menus[$key]['pages_path'] = $val['pages_path'];
				if (empty($menus[$key]['pages_path'])) {
					$menus[$key]['pages_path'] = '';
				}

				//vue3
				$menus[$key]['component'] = str_replace('.vue', '', $val['pages_path']);
				$menus[$key]['paths'] = $val['path'];
				$menus[$key]['perms'] = $val['path'];
				$menus[$key]['selected'] = $val['selected'];
				$menus[$key]['params'] = $val['params'];
				$menus[$key]['is_show'] = $val['is_show'];
				$menus[$key]['is_cache'] = $val['is_cache'];

				if ($val['type'] == 0) {
					$menus[$key]['type'] = 'M';
				} elseif ($val['type'] == 1) {
					$menus[$key]['type'] = 'C';
					unset($menus[$key]['children']);
				} elseif ($val['type'] == 2) {
					$menus[$key]['type'] = 'A';
				}
			}

			$retmenus = _generateListTree($menus, 0, ['id', 'pid']);
			return $retmenus;
		}
	}

	function del($model)
	{
		$idx =  $this->request->post('id', '', 'serach_in');
		if (!$idx) throw new ValidateException('参数错误');
		if (!is_array($idx)) {
			$idx = explode(',', $idx);
		}
		$model->destroy(['id' => $idx], true);
		return $this->json(['msg' => '操作成功']);
	}

	protected function json($result)
	{
		if (empty($result['code'])) {
			$result['code'] = 2000;
		}
		if (is_array($result['data'])) {
			if ($result['data']['current_page']) {
				$result['data']['lists'] = $result['data']['data'];
				$result['data']['count'] = $result['data']['total'];
				$result['data']['page_no'] = $result['data']['current_page'];
				$result['data']['page_size'] = $result['data']['per_page'];
			}
		}
		if (empty($result['no_replace'])) {
			$result = TextReplace::setreplace($result);
		}
		return json($result);
	}

	//获取要加载的组件
	protected function getComponents($menu)
	{
		$components = [];
		foreach ($menu as $v) {
			$components[] = [
				'name' => $v['name'],
				'path' => $v['path'],
				'meta' => ['title' => $v['title']],
				'pages_path' => $v['pages_path']
			];
			if ($v['children']) {
				$components = array_merge($components, $this->getComponents($v['children']));
			}
		}
		return $components;
	}
	public function getRoot()
	{
		return dirname(dirname(dirname(__DIR__)));
	}
	public function __call($method, $args)
	{
		throw new FuncNotFoundException('方法不存在', $method);
	}
}
