<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\RotarytablePrize;
use app\model\Coupon;

class RotarytableprizeController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');

		RotarytablePrize::datainitial();

		$query = RotarytablePrize::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}
		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['ptype'] = getPrizerptype($vo['ptype']);
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		RotarytablePrize::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		$data['begin_date'] = strtotime($data['begin_date']);
		$data['end_date'] = strtotime($data['end_date']);

		if (empty($id)) {
			$data['weid'] = weid();
			if (!empty($this->sid)) {
				$data['sid'] = $this->sid;
			}
			try {
				$res = RotarytablePrize::create($data);
				if ($res->id && empty($data['sort'])) {
					RotarytablePrize::update(['sort' => $res->id, 'id' => $res->id]);
				}
				$data['id'] = $res->id;
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				RotarytablePrize::update($data);
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
		$data = RotarytablePrize::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new RotarytablePrize());
	}
	function getField()
	{
		$data['ptypearray'] = getPrizerptype();
		$data['couponarray'] = Coupon::getpcarray();
		return $this->json(['data' => $data]);
	}
}
