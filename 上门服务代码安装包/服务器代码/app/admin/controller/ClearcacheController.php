<?php

namespace app\admin\controller;

use app\model\UsersSessions;

class ClearcacheController extends Base
{

	public function index()
	{
		UsersSessions::where('token', '<>', $this->getToken())->delete();

		return $this->json(['data' => '清除缓存功成!']);
	}
}
