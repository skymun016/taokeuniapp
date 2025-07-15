<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\OrderStatus;
use app\model\OrderCount;
use app\model\Order;

class OrderstatusController extends Base
{
    public function list()
    {
        $OrderStatus = OrderStatus::where('status', 1)
            ->order('id asc')
            ->select()
            ->toArray();
        foreach ($OrderStatus as $vo) {
            $data[$vo['id']] = $vo['name'];
        }

        return $this->json(['data' => $data]);
    }

    public function listname()
    {
        $ptype = input('get.ptype', '', 'serach_in');

        $data = OrderStatus::listname($ptype);

        return $this->json(['data' => $data]);
    }

    public function settings()
    {
        $OrderStatus = OrderStatus::field('id,name')
            ->where('status', 1)
            ->order('id asc')
            ->select()
            ->toArray();

        $data = $OrderStatus;
        return $this->json(['data' => $data]);
    }
}
