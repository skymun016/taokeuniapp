<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Viporder;


class ViporderController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = Viporder::where(['weid' => $weid]);

		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		if (!empty($this->ocid)) {
			$query->where('ocid', $this->ocid);
		}

		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;

		return $this->json($data);
	}

	function delete()
	{
		return $this->del(new Viporder());
	}
}
