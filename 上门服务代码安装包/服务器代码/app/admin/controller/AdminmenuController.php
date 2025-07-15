<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\AdminMenu;

class AdminmenuController extends Base
{

	function index()
	{
		if ($this->userInfo['username'] == 'admin') {
			$keyword = input('post.keyword', '', 'serach_in');
			$query = AdminMenu::field('*');

			if (!empty($keyword)) {
				$query->where('title', 'like', '%' . $keyword . '%');
			}

			$list = $query->order('sort asc')->select()->toArray();
			//var_dump($query->getLastSql());
			if (!empty($keyword)) {
				$data['data'] = $list;
			} else {
				$data['data'] = _generateListTree($list, 0, ['id', 'pid']);
			}

			return $this->json($data);
		}
	}

	function listUpdate()
	{
		if ($this->userInfo['username'] == 'admin') {
			$data = only('id,is_console,is_admin,is_city,is_tuanzhang,is_store,is_v2,is_v3,is_v6,status,sort');
			if (!$data['id']) throw new ValidateException('参数错误');
			AdminMenu::update($data);

			return $this->json(['msg' => '操作成功']);
		}
	}

	public function update()
	{
		if ($this->userInfo['username'] == 'admin') {
			$id = $this->request->post('id');
			$data = input('post.');
			$data['pid'] = (int)$data['pid'];

			if (empty($id)) {
				try {
					$res = AdminMenu::create($data);
					if ($res->id && empty($data['sort'])) {
						AdminMenu::update(['sort' => $res->id, 'id' => $res->id]);
					}
				} catch (\Exception $e) {
					throw new ValidateException($e->getMessage());
				}

				return $this->json(['msg' => '添加成功', 'data' => $res->id]);
			} else {

				try {
					AdminMenu::update($data);
				} catch (\Exception $e) {
					throw new ValidateException($e->getMessage());
				}
				return $this->json(['msg' => '修改成功']);
			}
		}
	}

	function getInfo()
	{
		if ($this->userInfo['username'] == 'admin') {
			$id =  $this->request->post('id', '', 'serach_in');
			if (!$id) throw new ValidateException('参数错误');
			$field = '*';
			$res = AdminMenu::field($field)->find($id);
			return $this->json(['data' => $res]);
		}
	}

	function delete()
	{
		if ($this->userInfo['username'] == 'admin') {
			return $this->del(new AdminMenu());
		}
	}

	function getField()
	{
		$data['pids'] = _generateSelectTree(AdminMenu::getpidarray());
		return $this->json(['data' => $data]);
	}
}
