<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\RecoveryCategory;
use app\model\ServiceTimeptype;

class RecoverycategoryController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$ptype = input('post.ptype', '', 'serach_in');

		$query = RecoveryCategory::where('weid', weid());

		if (!empty($ptype)) {
			$query->where('ptype', $ptype);
		}
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$list = $query->order('sort asc')->select()->toArray();

		foreach ($list as &$vo) {
			$vo['image'] = toimg($vo['image']);
			if ($vo['ptype'] == 1) {
				$vo['deliverymodename'] = getgoodsdeliverymodename($vo['deliverymode']);
			} elseif ($vo['ptype'] == 2) {
				$vo['deliverymodename'] = getservicedeliverymodename($vo['deliverymode']);
				$vo['servicetime_ptype'] = ServiceTimeptype::getTitle($vo['servicetime_ptype']);
			}
			$vo['ptype'] = getptype($vo['ptype']);
		}
		$data['data'] = _generateListTree($list, 0, ['id', 'pid']);

		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,is_binding,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		RecoveryCategory::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);
		$data['pid'] = (int)$data['pid'];
		if (!empty($data['deliverymode'])) {
			$data['deliverymode'] = implode(',', $data['deliverymode']);
		}

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = RecoveryCategory::create($data);
				if ($res->id && empty($data['sort'])) {
					RecoveryCategory::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				RecoveryCategory::update($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id = input('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$res = RecoveryCategory::find($id);
		if ($res) {
			$res = $res->toArray();
			$res['image'] = toimg($res['image']);
			if (empty($res['deliverymode'])) {
				$res['deliverymode'] = [];
			} else {
				$res['deliverymode'] =  explode(',', $res['deliverymode']);
			}
		}

		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new RecoveryCategory());
	}

	function getField()
	{
		$ptype = input('post.ptype', '', 'serach_in');
		$data['pids'] = _generateSelectTree(RecoveryCategory::getpcarray($ptype));
		$data['ptypearray'] = getptype();
		$data['goodsdeliverymodearray'] = getgoodsdeliverymode();
		$data['servicedeliverymodearray'] = getservicedeliverymode();
		$data['servicetime_ptypearray'] = ServiceTimeptype::getpcarray();
		return $this->json(['data' => $data]);
	}
	function getTree()
	{

		$alldata[0]['val'] = '0';
		$alldata[0]['key'] = '全部分类';
		$alldata[0]['pid'] = 0;

		$cdata = _generateSelectTree(RecoveryCategory::getpcarray());

		$data = array_merge($alldata, $cdata);
		return $this->json(['data' => $data]);
	}
}
