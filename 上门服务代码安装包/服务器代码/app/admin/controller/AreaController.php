<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use Overtrue\Pinyin\Pinyin;
use app\model\Area;

class AreaController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$parent_id =  input('post.parent_id', '', 'serach_in');

		if (empty($parent_id)) {
			$parent_id = 0;
		}

		$query = Area::where('area_parent_id', $parent_id);

		if (!empty($keyword)) {
			$query->where('area_name|keyword', 'like', '%' . $keyword . '%');
		}
		$pages = getpage();
		$pages['list_rows'] = 50;
		$res = $query->order('area_sort asc,id asc')
			->paginate($pages)
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,area_sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Area::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		$data['area_parent_id'] = (int)$data['area_parent_id'];

		$pinyin = new Pinyin();
		$data['letter'] = strtoupper($pinyin->permalink($data['area_name'], '')[0]);
		$data['keyword'] = $data['area_name'] . strtoupper($pinyin->permalink($data['area_name'], ''));

		if (empty($id)) {
			try {
				$res = Area::create($data);
				if ($res->id && empty($data['area_sort'])) {
					Area::update(['area_sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				Area::update($data);
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
		$res = Area::find($id);
		if ($res) {
			$res = $res->toArray();
		}

		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new Area());
	}

	function getField()
	{
		$data['pids'] = _generateSelectTree(Area::getpcTree());

		return $this->json(['data' => $data]);
	}

	function getpcTree()
	{
		$data['data'] = Area::getpcTree();
		return $this->json($data);
	}
	public function geteltree()
	{
		$data = $this->eltree(0);

		return $this->json(['data' => $data]);
	}

	private function eltree($pid)
	{
		$field = 'id,area_name';
		$list = Area::field($field)
			->where(['area_parent_id' => $pid])
			->select()
			->toArray();
		if ($list) {
			foreach ($list as $key => $val) {
				$datas[$key]['id'] = $val['id'];
				$datas[$key]['title'] = $val['area_name'];

				$sublist = Area::field($field)
					->where(['area_parent_id' => $val['id']])
					->select()
					->toArray();
				if ($sublist) {
					$datas[$key]['children'] = $this->eltree($val['id']);
				}
			}
			if(is_array($datas)){
				$datas = array_values($datas);
			}
			return $datas;
		}
	}
}
