<?php

namespace app\admin\controller\kefu;

use think\exception\ValidateException;
use app\model\kefu\Seatinggroups;

class SeatinggroupsController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$status = input('post.status', '', 'serach_in');
		$weid = weid();
		$where = [];
		$where['weid'] = $weid;
		if ($status !== '') {
			$where['status'] = $status;
		}

		$field = 'id,title,touxiang,status,px';

		$query = Seatinggroups::where($where);

		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}
		$res = $query->field($field)
			->order('id desc')
			->paginate(getpage())->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Seatinggroups::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{

		$id = $this->request->post('id');
		$data = input('post.');
		if (empty($id)) {
			$weid = weid();
			$data['weid'] = $weid;
			$res = Seatinggroups::create($data);
		return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			Seatinggroups::update($data);
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) $this->error('参数错误');
		$res = Seatinggroups::find($id);
		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new Seatinggroups());
	}
}
