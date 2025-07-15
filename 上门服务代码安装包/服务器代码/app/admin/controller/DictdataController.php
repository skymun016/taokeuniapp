<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\DictData;

class DictdataController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = DictData::where(['weid' => $weid]);
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
		DictData::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = DictData::create($data);
				if ($res->id && empty($data['sort'])) {
					DictData::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				DictData::update($data);
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
		$data = DictData::find($id);

		if ($data) {
			$data = $data->toArray();
			$data['pic'] = toimg($data['pic']);
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new DictData());
	}

	function getoptions()
	{
		$type = input('get.type', '', 'serach_in');
		if (!is_string($type)) {
			return [];
		}

		$type = explode(',', $type);
		$lists = DictData::whereIn('type_value', $type)->select()->toArray();

		if (empty($lists)) {
			return [];
		}

		$result = [];
		foreach ($type as $item) {
			foreach ($lists as $dict) {
				if ($dict['type_value'] == $item) {
					$result[$item][] = $dict;
				}
			}
		}
		return $this->json(['data' => $result]);
	}
}
