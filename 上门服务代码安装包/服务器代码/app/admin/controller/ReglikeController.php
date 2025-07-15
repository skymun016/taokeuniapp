<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Reglike;

class ReglikeController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = Reglike::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('sort asc,id asc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['pic'] = toimg($vo['pic']);
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Reglike::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = Reglike::create($data);
				if ($res->id && empty($data['sort'])) {
					Reglike::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				Reglike::update($data);
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
		$data = Reglike::find($id);

		if ($data) {
			$data = $data->toArray();
			$data['pic'] = toimg($data['pic']);
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new Reglike());
	}
}
