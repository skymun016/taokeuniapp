<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Authorization;

class AuthorizationController extends Base
{

	public function update()
	{
		$postdata = $this->request->post();
		if (!empty($postdata['id'])) {
			$updata['ip'] = trim($postdata['ip']);
			$updata['domainname'] = trim($postdata['domainname']);
			$updata['secret'] = trim($postdata['secret']);
			Authorization::where('id', $postdata['id'])->update($updata);
		}
		return $this->json(['msg' => '操作成功','show'=>1]);
	}

	function getInfo()
	{
		$res = Author()::authorizationInfo();
		return $this->json(['data' => $res]);
	}
}
