<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\TuanFound;
use app\model\TuanGoods;
use app\model\TuanFollow;

class TuanfoundController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = TuanFound::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}
		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['found_time'] = time_format($vo['found_time']);
			$vo['found_end_time'] = time_format($vo['found_end_time']);
			$vo['tuan_end_time'] = time_format($vo['tuan_end_time']);
			$vo['TuanGoods'] = TuanGoods::getTuanGoods($vo['tuan_id']);
			$vo['status'] = tuanFoundStatus($vo['status']);
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		TuanFound::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		if (!empty($id)) {
			try {
				TuanFound::update($data);
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
		$data = TuanFound::find($id)->toArray();
		if (!empty($data['tuan_id'])) {
			$data['TuanGoods'] = TuanGoods::getTuanGoods($data['tuan_id']);
		}

		$data['TuanFollow'] = TuanFollow::getTuanFollow($data['id']);
		foreach ($data['TuanFollow'] as &$vo) {
			if($vo['pay_time']>0){
				$vo['pay_time'] = time_format($vo['pay_time']);
			}else{
				$vo['pay_time'] = '未支付';
			}
			$vo['is_robot'] = yesno($vo['is_robot']);
			$vo['status'] = tuanFoundStatus($vo['status']);
		}

		$data['found_time'] = time_format($data['found_time']);
		$data['found_end_time'] = time_format($data['found_end_time']);
		$data['tuan_end_time'] = time_format($data['tuan_end_time']);
		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new TuanFound());
	}
}
