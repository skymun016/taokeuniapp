<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Files;
use app\model\FilesCate;

class FilesController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = Files::where(['weid' => $weid]);

		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('sort asc,id asc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Files::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	function delete()
	{
		return $this->del(new Files());
	}
	function getField()
	{
		$data['cidarray'] = FilesCate::getpagearray();

		return $this->json(['data' => $data]);
	}
}
