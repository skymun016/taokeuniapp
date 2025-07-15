<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Lang;

class LangController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$query = Lang::where(['weid' => weid()]);
		Lang::datainitial();
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$Langdata = $query->order('sort asc,id asc')->select()->toArray();

		$data['data'] = $Langdata;
		$data['no_replace'] = 1;
		return $this->json($data);
	}
	public function getlang()
    {
        $data = Lang::getLang();
        return $this->json(['data' => $data, 'no_replace' => 1]);
    }

	function listUpdate()
	{
		$data = only('id,title,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Lang::update($data);

		return $this->json(['msg' => '操作成功']);
	}
}
