<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\OrderCount;
use app\model\OrderStatus;

class OrdercountController extends Base
{

    public function userordercount()
    {

        $where['weid'] = weid();
        $where['uid'] = UID();

        $goodsStatus = OrderStatus::listname('goods');

        foreach ($goodsStatus as $vo) {
            $data['goodsorder'][$vo['val']] = OrderCount::getUserCount(UID(), $vo['val']);
        }

        $serviceStatus = OrderStatus::listname('service');

        foreach ($serviceStatus as $vo) {
            $data['serviceorder'][$vo['val']] = OrderCount::getUserCount(UID(), $vo['val']);
        }

        return $this->json(['data' => $data]);
    }
}
