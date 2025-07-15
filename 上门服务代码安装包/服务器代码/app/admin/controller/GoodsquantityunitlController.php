<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\GoodsQuantityUnit;

class GoodsquantityunitlController extends Base
{
	function getPtype()
	{
		$path = input('post.path', '', 'serach_in');
		if ($path == '/goodsquantityunitl/service') {
			return 2;
		} else {
			return 1;
		}
	}

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$ptype = $this->getPtype();
		GoodsQuantityUnit::datainitial($ptype);

		$query = GoodsQuantityUnit::where(['weid' => $weid, 'ptype' => $ptype]);
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
		GoodsQuantityUnit::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);
		$weid = weid();
		$ptype = $this->getPtype();

		if (empty($id)) {
			$data['weid'] = $weid;
			$data['ptype'] = $ptype;
			try {
				$res = GoodsQuantityUnit::create($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				GoodsQuantityUnit::update($data);
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
		$data = GoodsQuantityUnit::find($id);

		if ($data) {
			$data = $data->toArray();
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new GoodsQuantityUnit());
	}
}
