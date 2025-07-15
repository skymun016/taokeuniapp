<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Points;

class PointsController extends Base
{

	function index()
	{
		$create_time = input('post.create_time', '', 'serach_in');
		$query = Points::where('id', '>', 0);

		if (!empty($create_time)) {
			$query->where('create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]);
		}
		$query->with(['member']);

		$res = $query->order('id desc')->paginate(getpage())->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function delete()
	{
		return $this->del(new Points());
	}
}
