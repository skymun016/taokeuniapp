<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\UuidRelation;
use app\model\Technical;
use app\model\Config;
use app\model\Category;
use app\model\Order;


class AdmintechnicalController extends Base
{

	public function check()
	{
		$data = Technical::getInfobyuid(UID());
		$message = '';
		$is_login = 0;

		if ($data['status'] != 1) {
			$data = 0;
			$message = '请先登录！';
		} else {
			$is_login = 1;
			Order::autosettlement();
			$data['statistical'] = $this->statistical($data['uuid']);
			$data['Config'] = Config::getconfig('technical');
			if (empty($data['Config'])) {
				$data['Config'] = [];
			}
		}
		return $this->json(['message' => $message,'is_login' => $is_login, 'data' => $data]);
	}
	//营业状态
	public function business()
	{
		$id = $this->request->post('id');
		$Technical = Technical::find($id);
		if (!empty($Technical)) {
			if ($Technical->is_business == 1) {
				$Technical->is_business = 0;
			} else {
				$Technical->is_business = 1;
			}
			try {
				$Technical->save();
				$data = $Technical->toArray();
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
		} else {
			$data = [];
		}
		return $this->json(['msg' => '操作成功', 'data' => $data]);
	}

	public function statistical($uuid)
	{
		$query = Order::where(['weid' => weid(), 'ptype' => 2]);
		$withJoin = [
			'staff' => ['order_id', 'uuid', 'title', 'end_time', 'begin_time'],
		];
		$query->withJoin($withJoin, 'left');
		$query->where(['staff.uuid' => $uuid]);

		$query2 = $query;
		$statistical['status2'] = $query2->where('order_status_id', 2)->count();

		$query3 = $query;
		$statistical['status3'] = $query3->where('order_status_id', 3)->count();

		$query5 = $query;
		$statistical['status5'] = $query5->where('order_status_id', 5)->count();

		$query4 = $query;
		$statistical['status4'] = $query4->where('order_status_id', 4)->count();

		return $statistical;
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		try {
			Technical::update($data);
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
		return $this->json(['msg' => '修改成功']);
	}

	function getInfo()
	{
		$uuid = UuidRelation::getuuid(UID(), 'technical');
		$data = Technical::where(['uuid' => $uuid])->find();
		if (!empty($data)) {
			$data = $data->toArray();
		} else {
			$data = Technical::where(['uid' => UID()])->find();
			if (!empty($data)) {
				$data = $data->toArray();
				if (!empty($uuid)) {
					Technical::where('id', $data['id'])->update(['uuid' => $uuid]);
				}
			}
		}

		if (!empty($data)) {
			$data = $data->toArray();
		}
		//加空判定
		if (empty($data['cate_ids'])) {
			$data['cate_ids'] = [];
		} else {
			$data['cate_ids'] = explode(',', $data['cate_ids']);
		}
		$data['Category'] = Category::gettoparray();


		return $this->json(['data' => $data]);
	}
}
