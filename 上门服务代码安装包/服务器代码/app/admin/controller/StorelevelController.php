<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\StoreLevel;

class StorelevelController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = StoreLevel::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$datalist = $query->order('sort asc,id asc')->select()->toArray();

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		StoreLevel::update($data);

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
				$res = StoreLevel::create($data);
				if ($res->id && empty($data['sort'])) {
					StoreLevel::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				StoreLevel::update($data);
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
		$data = StoreLevel::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new StoreLevel());
	}
}
