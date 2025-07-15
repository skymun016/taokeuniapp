<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Member;
use app\model\MemberCashlogs;
use app\model\Order;
use app\model\Tongue;
use app\model\Viporder;
use app\model\Test;
use app\model\Config;

class PayController extends Base
{
    public function index()
    {

        $orderid = intval(input('post.orderid', '', 'serach_in'));
        $money = input('post.money', '', 'serach_in');
        $money = round($money, 2);
        $money = floatval($money * 100);
        $type = input('post.type', '', 'serach_in');
        if ($type == "order") {
            if (!empty($orderid)) {
                $orderdata =  Order::find($orderid);
                if (!empty($orderdata)) {
                    $orderdata = $orderdata->toArray();
                } else {
                    return $this->json(['errno' => 1, 'msg' => '订单不存在']);
                }

                //构造支付参数
                $paydata = array(
                    'body'          => '订单支付',
                    'out_trade_no'  => $orderdata['order_num_alias'],
                    'total_fee'     => $money,
                );
            }
        } elseif ($type == "vip") {
            $viporderdata =  Viporder::find($orderid);
            $paydata = array(
                'body'          => '购买会员等级',
                'out_trade_no'  => 'vip-' . $viporderdata['order_num_alias'],
                'total_fee'     => $money,
            );
        } elseif ($type == "additional") {
            if (!empty($orderid)) {
                $orderdata =  Order::find($orderid);
                if (!empty($orderdata)) {
                    $orderdata = $orderdata->toArray();
                } else {
                    return $this->json(['errno' => 1, 'msg' => '订单不存在']);
                }

                //构造支付参数
                $paydata = array(
                    'body'          => '支付尾款',
                    'out_trade_no'  => 'additional-' . $orderdata['order_num_alias'],
                    'total_fee'     => $money,
                );
            }
        } elseif ($type == "paybill") {
            $paydata = array(
                'body'          => '优惠买单',
                'out_trade_no'  => 'paybill-' . time(),
                'total_fee'     => $money,
            );
        } elseif ($type == "recharge") {

            $paydata = array(
                'body'          => '充值',
                'out_trade_no'  => 'recharge-' . time(),
                'total_fee'     => $money,
            );
        } elseif ($type == "tongue") {

            $paydata = array(
                'body'          => 'AI舌诊',
                'out_trade_no'  => 'tongue-' . $orderid,
                'total_fee'     => $money,
            );
        }

        //生成支付参数，返回给小程序端
        $pay_params = $this->wxpay($paydata);

        //$pay_params['url'] = gethost() . TP_APIURL .'/index.php/index/pay/notify/state/'.input('get.state');

        return $this->json($pay_params);
    }

    public function wxpay($paydata)
    {
        // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        $paydata['notify_url'] = gethost() . TP_APIURL . '/index.php/index/pay/notify/state/' . input('get.state', '', 'serach_in').'/from/' . input('get.from', '', 'serach_in');
        $paydata['trade_type'] = 'JSAPI';
        $paydata['openid'] = getFans()['openid'];
        $payment = \app\samos\wechat\WxPaymethod::makepay($this->userInfo['ptype']);
        $result = $payment->order->unify($paydata);
        
        //Test::create(['title' => '微信支付信息', 'info' => serialize($paydata)]);

        if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            $msg = '支付失败';
            return ['errno' => 1, 'msg' => $msg, 'message' => $msg, 'data' => $result];
        } else {
            $jssdk = $payment->jssdk;
            $payres = $jssdk->bridgeConfig($result['prepay_id'], false);
            return ['data' => $payres];
        }
    }

    //支付回调
    public function notify()
    {
        //$xml = file_get_contents("php://input"); //回调回来的数据
        //Test::create(['title' => '微信支付回调', 'info' => serialize($xml)]);
        //file_put_contents('log/'.time() . "backdata.txt", $xml); //数据写入文件
        $response = \app\samos\wechat\WxPaymethod::makepay($this->userInfo['ptype'])->handlePaidNotify(function ($message, $fail) {
            //Test::create(['title' => '微信支付回调3', 'info' => $message['return_code']]);
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态

                if ($message['result_code'] === 'SUCCESS') {
                    //支付成功
                    $tid = $message['out_trade_no'];
                    $tidarray = explode('-', $tid);

                    $message['total_fee'] = $message['total_fee'] / 100;

                    if (!empty($tidarray[1])) {
                        $tid = $tidarray[1];
                    }
                    if ($tidarray[0] == 'paybill') {

                        $cashlogsdata['uid'] = UID();
                        $cashlogsdata['weid'] = weid();
                        $cashlogsdata['id'] = '0';
                        $cashlogsdata['order_num_alias'] = '0';
                        $cashlogsdata['remarks'] = '优惠买单';
                        $cashlogsdata['prefix'] = 2;
                        $cashlogsdata['amount'] = $message['total_fee'];

                        MemberCashlogs::create($cashlogsdata);
                    } elseif ($tidarray[0] == 'recharge') {

                        Member::where('id', UID())
                            ->inc('balance', $message['total_fee'])
                            ->update();

                        $cashlogsdata['uid'] = UID();
                        $cashlogsdata['weid'] = weid();
                        $cashlogsdata['id'] = '0';
                        $cashlogsdata['order_num_alias'] = '0';
                        $cashlogsdata['remarks'] = '充值';
                        $cashlogsdata['prefix'] = 1;
                        $cashlogsdata['amount'] = $message['total_fee'];

                        MemberCashlogs::create($cashlogsdata);
                    } elseif ($tidarray[0] == 'additional') {

                        Order::itional_pay($tid);
                    } elseif ($tidarray[0] == 'tongue') {
                        Tongue::where('id', $tid)->update(['is_pay' => 1]);
                    } elseif ($tidarray[0] == 'vip') {
                        Viporder::pay_order(['order_num_alias' => $tid]);
                    } else {

                        $fee = $message['total_fee'];
                        if (!empty($fee)) {
                            $order = Order::find($tid);
                            if (!empty($order)) {
                                $order = $order->toArray();
                            }
                        }

                        $data['order_num_alias'] = $tid;
                        $data['user_agent'] = serialize($message);

                        $ordermod = new Order;
                        $ordermod->pay_order($data);
                    }
                    return true;
                } elseif ($message['result_code'] === 'FAIL') {
                    // 用户支付失败
                    return true;
                }
            } else {
                return false;
            }
        });

        return $response;
    }
}
