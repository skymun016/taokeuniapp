<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Rewardspenalties;
use app\model\Order;

class RewardspenaltiesController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$query = Rewardspenalties::where(['weid' => weid()]);
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
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Rewardspenalties::update($data);
		$comment = Rewardspenalties::find($data['id']);

		if ($comment) {
			Order::where('uuid', $comment->technical_uuid)->update(['comment' => Rewardspenalties::total_byuuid($comment->technical_uuid)]);
		}

		return $this->json(['msg' => '操作成功']);
	}
}
