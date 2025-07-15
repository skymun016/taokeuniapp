<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Reglike;

class ReglikeController extends Base
{
    public function index()
    {
        $data = Reglike::where(['weid' => weid()])
            ->order('sort asc')
            ->select()
            ->toArray();

        foreach ($data as &$vo) {
            $vo['pic'] = toimg($vo['pic']);
        }

        return $this->json(['data' => $data]);
    }
}
