<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\RefundAddress;

class RefundaddressController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = RefundAddress::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id asc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		RefundAddress::update($data);

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
				$res = RefundAddress::create($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				RefundAddress::update($data);
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
		$data = RefundAddress::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new RefundAddress());
	}
}
