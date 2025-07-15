<?php

namespace app\samos\controller;

use app\model\Goods;
use app\model\GoodsImage;
use app\model\Attribute;

class UpgradeController extends \app\BaseController
{

	public function index()
	{
		$files = dirname(dirname(dirname(__DIR__))) . '/upgrade.php';
		include $files;
		return json(['data' => '更新成功！']);
	}
}
