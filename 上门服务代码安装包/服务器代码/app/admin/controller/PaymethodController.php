<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Paymethod;

class PaymethodController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		Paymethod::datainitial();
		$query = Paymethod::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('sort asc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Paymethod::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		$data['settings'] = serialize($data['settings']);

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = Paymethod::create($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				Paymethod::update($data);
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
		$data = Paymethod::field('*')->find($id)->toArray();
		$data['settings'] = iunserializer($data['settings']);
		if(empty($data['settings'])){
			$data['settings'] = ['mchid'=>'','signkey'=>''];
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new Paymethod());
	}
}
