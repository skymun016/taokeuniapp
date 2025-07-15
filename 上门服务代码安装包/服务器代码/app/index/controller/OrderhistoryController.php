<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\OrderHistory;

class OrderhistoryController extends Base
{
    public function addhistory()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        $order_status_id = input('post.order_status_id', '', 'serach_in');

        $image = input('post.path', '', 'serach_in');
        $remark = input('post.remark', '', 'serach_in');
        $order_history['order_id'] = $orderid;
        $order_history['order_status_id'] = $order_status_id;
        $order_history['remark'] = $remark;
        $order_history['image'] = $image;
        OrderHistory::create($order_history);
        return $this->json(['data' => $data]);
    }
}
