<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Users;

class AdminController extends Base
{

	public function detail()
	{
		$message = '';
		$is_login = 0;
		$user = Users::getadminbyopenid();

		if ($user) {
			$is_login = 1;
			$data = $user;
		} else {
			$data = 0;
			$message = '您不是管理员！';
		}

		return $this->json(['message' => $message, 'is_login' => $is_login, 'data' => $data]);
	}
}
