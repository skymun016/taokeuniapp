<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Forhelp;

class ForhelpController extends Base
{

	/*
 	* @Description  数据列表
 	*/
	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$create_time = input('post.create_time', '', 'serach_in');

		$where = [];
		$where['weid'] = weid();
		$query = Forhelp::where($where);

		if (!empty($keyword)) {
			$query->where('username|tel', 'like', '%' . $keyword . '%');
		}

		if (!empty($create_time)) {
			$query->where('create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]);
		}
		$res = $query->order('id desc')->paginate(getpage())->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}


	/*
 	* @Description  删除
 	*/
	function delete()
	{
		return $this->del(new Forhelp());
	}


	/*
 	* @Description  查看详情
 	*/
	function detail()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$res = Forhelp::find($id);
		if (!empty($res)) {
			$res = $res->toArray();
		}
		return $this->json(['data' => $res]);
	}
}
