<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Order;
use app\model\Message;
use app\model\MessageMini;
use app\model\MessageMp;
use app\model\OrderStaff;

class MessageController extends Base
{
    public function payorder()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        $order_info = Order::find($orderid);
        if (!empty($order_info)) {
            $order_info = $order_info->toArray();
            if (!empty($order_info['sid'])) {
                MessageMp::storeOrderPay($order_info['sid'], $order_info);
            } else {
                $technicalId = OrderStaff::getuuid($order_info["id"]);
                if (empty($technicalId)) {
                    MessageMp::undertake($order_info);
                } else {
                    MessageMini::sendMiniStaff($order_info);
                    MessageMp::distribution($technicalId, $order_info);
                }
            }
            MessageMp::orderPayNoticeAdmin($order_info);
            MessageMini::sendMiniPaysuccess($order_info);
        }

        return $this->json(['data' => $order_info]);
    }

    public function refundorder()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        $order_info = Order::find($orderid);

        if (!empty($order_info)) {
            $order_info = $order_info->toArray();
            MessageMp::orderRefundNoticeAdmin($order_info);
        }

        return $this->json(['data' => $order_info]);
    }
}
