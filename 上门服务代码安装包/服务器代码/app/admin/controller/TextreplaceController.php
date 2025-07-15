<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\TextReplace;

class TextreplaceController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = TextReplace::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('sort asc,id asc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		$data['no_replace'] = 1;

		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		TextReplace::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = TextReplace::create($data);
				if ($res->id && empty($data['sort'])) {
					TextReplace::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				TextReplace::update($data);
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
		$data = TextReplace::find($id);

		if ($data) {
			$data = $data->toArray();
		}
		return $this->json(['data' => $data, 'no_replace' => 1]);
	}

	function delete()
	{
		return $this->del(new TextReplace());
	}
}
