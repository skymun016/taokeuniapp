<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\MemberTask;
use app\model\Coupon;
use app\model\Goods;

class MembertaskController extends Base
{
	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$mgid = input('post.mgid', '', 'serach_in');
		$query = MemberTask::where(['mgid' => $mgid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$datalist = $query->order('sort asc,id asc')->select()->toArray();

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,is_lookprice,is_buyright,is_default,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		MemberTask::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = MemberTask::create($data);
				if ($res->id && empty($data['sort'])) {
					MemberTask::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				MemberTask::update($data);
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
		$data = MemberTask::field('*')->find($id)->toArray();

		if (!empty($data['goods_id'])) {
			$goods = Goods::find($data['goods_id']);
			if (!empty($goods)) {
				$data['goods'] = $goods->toArray();
			}
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new MemberTask());
	}
	function getField()
	{
		$data['couponarray'] = Coupon::getpcarray(2);
		return $this->json(['data' => $data]);
	}
}
