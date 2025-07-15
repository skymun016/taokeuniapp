<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Lang;

class LangController extends Base
{

    public function index()
    {
        $where['weid'] = weid();

        $where['status'] = 1;
        $listdata = Lang::where($where)
            ->order('sort asc')
            ->select()
            ->toArray();

        foreach ($listdata as $key => $vo) {
            $data[$vo['item']] = $vo['title'];

        }

        return $this->json(['data' => $data]);
    }
}
