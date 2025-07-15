<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\OrderRefund;
use app\model\MessageMp;
use app\model\Order;
use app\model\Member;

class OrderrefundController extends Base
{
    public function create()
    {
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();

        $order = Order::find(input('post.order_id', '', 'serach_in'));

        if (!empty($order)) {
            if ($order->order_status_id == 2) {
                $postdata['refund_type'] = 1;
            } elseif ($order->order_status_id == 5) {
                $postdata['refund_type'] = 2;
            } elseif ($order->order_status_id == 7) {
                $postdata['refund_type'] = 2;
            }
            $postdata['weid'] = weid();
            $postdata['uid'] = $memberinfo['id'];
            $postdata['order_id'] = input('post.order_id', '', 'serach_in');
            $postdata['refund_price'] = input('post.refund_price', '', 'serach_in');
            $postdata['order_refund_no'] = build_order_no();
            $postdata['lianxiren'] = input('post.lianxiren', '', 'serach_in');
            $postdata['tel'] = input('post.tel', '', 'serach_in');
            $postdata['refund_desc'] = input('post.refund_desc', '', 'serach_in');
            $postdata['pic_list'] = input('post.pic_list', '', 'serach_in');
            $postdata['addtime'] = time();

            $r = OrderRefund::create($postdata);

            Order::update(['order_status_id' => 6, 'id' => $postdata['order_id']]);

            order::update(['id' => $postdata['order_id'], 'uid' => $postdata['uid'], 'order_status_id' => 6]);
        }

        if (empty($r)) {
            $errno = 1;
            $message = '申请失败';
        } else {
            $errno = 0;
            $message = '申请成功，等待处理！';
        }
        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }

    public function goodssend()
    {
        $order_id = input('post.order_id', '', 'serach_in');

        $postdata['order_id'] = $order_id;
        $postdata['is_user_send'] = input('post.is_user_send', '', 'serach_in');
        $postdata['user_send_express'] = input('post.user_send_express', '', 'serach_in');
        $postdata['user_send_express_code'] = input('post.user_send_express_code', '', 'serach_in');
        $postdata['user_send_express_no'] = input('post.user_send_express_no', '', 'serach_in');

        $r = OrderRefund::update($postdata);

        if (empty($r)) {
            $errno = 1;
            $message = '提交失败';
        } else {
            $errno = 0;
            $message = '提交成功，等待处理！';
        }

        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }
}
