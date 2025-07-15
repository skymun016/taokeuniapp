<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\GoodsBuynowinfo;

class GoodsbuynowinfoController extends Base
{

    public function getbuynowinfo()
    {

        $buynowinfoid = (int) input('post.buynowinfoid', '', 'intval');
        $buynowinfo = GoodsBuynowinfo::where(['id' => $buynowinfoid])->find();

        if (!empty($buynowinfo)) {
            $buynowinfo = $buynowinfo->toArray();
            $data = iunserializer($buynowinfo['data']);;
        }
        return $this->json(['data' => $data]);
    }
}
