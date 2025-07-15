<?php

namespace app\index\controller\kefu;

use think\exception\ValidateException;
use app\model\kefu\Seating;

class SeatingController extends Base
{

	function index()
	{

		$where['status'] = 1;

		$field = 'id,title,chatid,touxiang,status,px';
		$query = Seating::where($where);
		$res = $query->field($field)
			->order('id desc')
			->select()
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listbygroups()
	{

		$res = Seating::getlistbygroups();
		$data['data'] = $res;

		return $this->json($data);
	}
}
