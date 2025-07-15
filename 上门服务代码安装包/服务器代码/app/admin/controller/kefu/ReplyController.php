<?php

namespace app\admin\controller\kefu;

use think\exception\ValidateException;
use app\model\kefu\Reply;

class ReplyController extends Base
{

	function index()
	{
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = trim(input('post.status', '', 'serach_in'));
		$weid = weid();
		$where = [];
		$where['weid'] = $weid;
		if ($status !== '') {
			$where['status'] = $status;
		}

		$query = Reply::where($where);

		if (!empty($keyword)) {
			$query->where('content', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Reply::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{

		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);
		if (empty($id)) {
			$weid = weid();
			$data['weid'] = $weid;
			$res = Reply::create($data);
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			Reply::update($data);
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) $this->error('参数错误');
		$res = Reply::find($id);
		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new Reply());
	}
}
