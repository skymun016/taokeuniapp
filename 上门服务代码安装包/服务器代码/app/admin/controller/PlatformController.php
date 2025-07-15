<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Platform;
use app\model\PlatformScheme;

class PlatformController extends Base
{

	function index()
	{

		$keyword = input('post.keyword', '', 'serach_in');
		$query = new Platform;
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('sort asc,id asc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['logo'] = toimg($vo['logo']);
			$vo['loginbgimg'] = toimg($vo['loginbgimg']);
			$vo['loginurl'] = gethost() . '/admin?i=' . $vo['id'];
			if ($vo['endtime']) {
				$vo['endtime'] = time_format($vo['endtime']);
			} else {
				$vo['endtime'] = '永久';
			}
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Platform::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);
		if ($data['endtime']) {
			$data['endtime'] = strtotime($data['endtime']);
		} else {
			$data['endtime'] = (int) $data['endtime'];
		}


		if (empty($id)) {
			try {
				$res = Platform::create($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				Platform::update($data);
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
		$data = Platform::getInfo($id);
		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new Platform());
	}
	function getField()
	{
		$data['schemearray'] = PlatformScheme::getpcarray();

		return $this->json(['data' => $data]);
	}
}
