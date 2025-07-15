<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\FilesCate;

class FilescateController extends Base
{

	function index()
	{
		$weid = weid();
		$query = FilesCate::where(['weid' => $weid]);
		$list = $query->order('sort asc')->select()->toArray();
		$data['data'] = _generateListTree($list, 0, ['id', 'pid']);
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		FilesCate::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = FilesCate::create($data);
				if ($res->id && empty($data['sort'])) {
					FilesCate::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				FilesCate::update($data);
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
		$data = FilesCate::find($id);

		if ($data) {
			$data = $data->toArray();
			$data['pic'] = toimg($data['pic']);
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new FilesCate());
	}
}
