<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\StoreLevel;
use app\model\Config;

class StorelevelController extends Base
{

    public function list()
    {

        if (!\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $where['weid'] = weid();
            $data = StoreLevel::where($where)
                ->order('sort asc')
                ->select()
                ->toArray();
        }
        return $this->json(['data' => $data]);
    }
}
