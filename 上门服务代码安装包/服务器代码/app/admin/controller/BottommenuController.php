<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\BottomMenu;
use app\model\BottomMenuOriginal;
use app\model\BottomMenuType;
use app\model\DiyPage;

class BottommenuController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$tid = input('post.tid', '', 'serach_in');
		$tid = str_ireplace('aaa', '', $tid);
		$path = input('post.path', '', 'serach_in');

		if ($path == '/bottommenu/index') {
			$mo = 'bottom';
		}

		if ($path == '/bottommenu/technical') {
			$mo = 'technical';
		}

		if ($path == '/bottommenu/store') {
			$mo = 'store';
		}

		if ($path == '/clientmenu/member') {
			$mo = 'member';
		}

		$query = BottomMenu::where(['weid' => $weid, 'tid' => (int) $tid, 'module' => $mo]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$datalist = $query->order('sort asc,id asc')->select()->toArray();

		if (empty($datalist)) {
			BottomMenu::datainitial($mo, $tid);
			$datalist = $query->order('sort asc,id asc')->select()->toArray();
		}

		foreach ($datalist as &$vo) {

			$vo['hump'] = yesno($vo['hump']);
			$vo['is_index'] = yesno($vo['is_index']);
			$vo['icon'] = toimg($vo['icon']);
			$vo['iconactive'] = toimg($vo['iconactive']);
		}
		$data['MenuType'] = BottomMenuType::getpagearray();
		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,is_submitaudit,is_index,hump,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		BottomMenu::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		$data['tid'] = (int) str_ireplace('aaa', '', $data['tid']);

		if (empty($id)) {
			$data['weid'] = weid();
			$data['module'] = 'bottom';

			if ($data['path'] == '/clientmenu/member') {
				$data['module'] = 'member';
			}

			if ($data['path'] == '/bottommenu/technical') {
				$data['module'] = 'technical';
			}

			if ($data['path'] == '/bottommenu/store') {
				$data['module'] = 'store';
			}
			if (empty($data['icon'])) {
				$Original = BottomMenuOriginal::where('url', $data['url'])->find();
				if ($Original) {
					$data['icon'] = $Original->icon;
				}
			}

			if (empty($data['iconactive'])) {
				$Original = BottomMenuOriginal::where('url', $data['url'])->find();
				if ($Original) {
					$data['iconactive'] = $Original->iconactive;
				}
			}

			try {
				$res = BottomMenu::create($data);
				if ($res->id && empty($data['sort'])) {
					BottomMenu::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				BottomMenu::update($data);
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
		$data = BottomMenu::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new BottomMenu());
	}
	function getField()
	{
		$data['urlarray'] = BottomMenuOriginal::getpagearray();

		$diypagearray =  DiyPage::getpagearray();
		if (!empty($diypagearray)) {
			$data['urlarray'] = array_merge($data['urlarray'], $diypagearray);
		}

		return $this->json(['data' => $data]);
	}
}
