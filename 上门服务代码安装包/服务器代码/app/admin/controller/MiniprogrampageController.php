<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\BottomMenuOriginal;

class MiniprogrampageController extends Base
{

	function index()
	{
		$keyword = trim(input('post.keyword', '', 'serach_in'));

		$query = BottomMenuOriginal::where('status',1);
		if (!empty($keyword)) {
			$query->where('title|url', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id asc')->select()->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}
}
