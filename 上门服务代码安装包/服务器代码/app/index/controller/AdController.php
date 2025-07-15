<?php
namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Ad;

class AdController extends Base
{
	public function indexlist() {
        $data = Ad::where(['weid' => weid()])
                ->order('sort asc')
                ->select()
				->toArray();

        foreach ($data as &$vo) {
            $vo['pic'] = toimg($vo['pic']);
        }
		return $this->json(['data' => $data]);
    }
	
}