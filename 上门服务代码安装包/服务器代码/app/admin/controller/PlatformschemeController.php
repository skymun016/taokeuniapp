<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\PlatformScheme;

class PlatformschemeController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = trim(input('post.status', '', 'serach_in'));

		if ($status !== '') {
			$where['status'] = $status;
		}

		$field = 'id,title,status,description';
		$query = PlatformScheme::where($where);

		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$query->field($field)->order('id asc');

		$datalist = $query->select()->toArray();

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		PlatformScheme::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$weid = weid();
		$id = $this->request->post('id');
		$data = input('post.');

		if (!empty($data['access'])) {
			$data['access'] = implode(',', $data['access']);
		}

		if (empty($id)) {
			$res = PlatformScheme::create($data);
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			PlatformScheme::update($data);
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) $this->error('参数错误');
		$res = PlatformScheme::find($id);
		if (!empty($res)) {
			$res = $res->toArray();
		}

		if(!empty($res['access'])) {
			$res['access'] = explode(',', $res['access']);
		}

		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new PlatformScheme());
	}
}
