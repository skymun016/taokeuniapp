<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Tuanzhang;
use app\model\Category;
use app\model\Order;
use app\model\Store;
use app\model\UuidRelation;

class AdmintuanzhangController extends Base
{

	public function check()
	{
		$message = '';
		$is_login = 0;

		$data = Tuanzhang::getInfobyuid(UID());

		if ($data['status'] != 1) {
			$data = 0;
			$message = '请先登录！';
		} else {
			$is_login = 1;
			$data['statistical'] = $this->statistical($data['uuid']);
		}
		return $this->json(['message' => $message, 'is_login' => $is_login, 'data' => $data]);
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
	public function setstorelogin()
	{
		$datatz = Tuanzhang::getInfobyuid(UID());
		if (!empty($datatz)) {
			$Store = Store::getStorebytzid($datatz['id']);

			if ($Store['uuid']) {
				$uuid = $Store['uuid'];
			}else{
				$uuid = uniqid(rand(1, 10000));
				Store::where('id', $Store['sid'])->update(['uuid' => $uuid]);
			}
			UuidRelation::create(['weid' => weid(), 'ptype' => 'store', 'uuid' => $uuid, 'uid' => UID()]);
		}
		return $this->json(['msg' => '操作成功', 'data' => $datatz]);
	}
	public function business()
	{
		$id = $this->request->post('id');

		$Tuanzhang = Tuanzhang::find($id);

		if ($Tuanzhang->is_business == 1) {
			$Tuanzhang->is_business = 0;
		} else {
			$Tuanzhang->is_business = 1;
		}

		try {
			$Tuanzhang->save();
			$data = $Tuanzhang->toArray();
			$data['statistical'] = $this->statistical($data['uuid']);
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
		return $this->json(['msg' => '操作成功', 'data' => $data]);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		try {
			Tuanzhang::update($data);
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
		return $this->json(['msg' => '修改成功']);
	}

	function getInfo()
	{
		$uuid = UuidRelation::getuuid(UID(), 'tuanzhang');
		$data = Tuanzhang::where(['uuid' => $uuid])->find();
		if (!empty($data)) {
			$data = $data->toArray();
		} else {
			$data = Tuanzhang::where(['uid' => UID()])->find();
			if (!empty($data)) {
				$data = $data->toArray();
				if (!empty($uuid)) {
					Tuanzhang::where('id', $data['id'])->update(['uuid' => $uuid]);
				}
			}
		}

		if (!empty($data)) {
			$data = $data->toArray();
		}
		//空判定
		if (empty($data['cate_ids'])) {
			$data['cate_ids'] = [];
		} else {
			$data['cate_ids'] = explode(',', $data['cate_ids']);
		}
		$data['Category'] = Category::gettoparray();


		return $this->json(['data' => $data]);
	}
}
