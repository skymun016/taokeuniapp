<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\HospitalDepartments;

class HospitaldepartmentsController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');

		$query = HospitalDepartments::where('weid', weid());

		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$list = $query->order('sort asc')->select()->toArray();

		foreach ($list as &$vo) {
			$vo['image'] = toimg($vo['image']);
		}
		$data['data'] = _generateListTree($list, 0, ['id', 'pid']);

		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,is_binding,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		HospitalDepartments::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);
		$data['pid'] = (int)$data['pid'];

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = HospitalDepartments::create($data);
				if ($res->id && empty($data['sort'])) {
					HospitalDepartments::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				HospitalDepartments::update($data);
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
		$res = HospitalDepartments::find($id);
		if ($res) {
			$res = $res->toArray();
			$res['image'] = toimg($res['image']);
			
		}

		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new HospitalDepartments());
	}

	function getField()
	{
		$data['pids'] = _generateSelectTree(HospitalDepartments::getpcarray());
		return $this->json(['data' => $data]);
	}
	function getTree()
	{

		$alldata[0]['val'] = '0';
		$alldata[0]['key'] = '全部分类';
		$alldata[0]['pid'] = 0;
		
		$cdata = _generateSelectTree(HospitalDepartments::getpcarray());

		$data = array_merge($alldata,$cdata);
		return $this->json(['data' => $data]);
	}
}
