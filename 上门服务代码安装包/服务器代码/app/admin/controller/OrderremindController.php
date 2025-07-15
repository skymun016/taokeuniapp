<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\OrderRemind;

class OrderremindController extends Base
{

	function index()
	{
		$weid = weid();
		$query = OrderRemind::where(['weid' => $weid]);

		$res = $query->order('id asc')->find();
		if (!empty($res)) {
			$data['data'] = $res->toArray();
			//OrderRemind::where('id',$data['data']['id'])->delete();
		}

		return $this->json($data);
	}

	function delete()
	{
		$id  = input('post.id', '', 'intval');
		if (!empty($id)) {
			OrderRemind::where('id',$id)->delete();
		}

		return $this->json($data);
	}
}
