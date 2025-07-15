<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\OrderTimescard;
use app\model\OrderTimescardRecord;

class OrdertimescardtecordController extends Base
{
    public function writeoff()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        $uid = input('post.uid', '', 'serach_in');
        $is_timing = input('post.is_timing', '', 'serach_in');
        $time = time();

        $is_writeoff = 1;

        if ($is_timing == 1) {
            $week = date("w");
            if ($week === 0) {
                $week = 7;
            }

            $day = date("j");

            $OrderTimescard = OrderTimescard::where(['timestype' => 0, 'yue_date' => $week, 'order_id' => $orderid])->find();

            if (empty($OrderTimescard)) {
                $OrderTimescard = OrderTimescard::where(['timestype' => 1, 'yue_date' => $day, 'order_id' => $orderid])->find();
            }

            if (empty($OrderTimescard)) {
                $is_writeoff = 0;
                $message = '今天不是预约的时间！';
            }
        }
        $otr = OrderTimescardRecord::where(['create_day' => strtotime(time_ymd($time)), 'order_id' => $orderid])->find();

        if (!empty($otr)) {
            $is_writeoff = 0;
            $message = '今天已核销！';
        }
        if ($is_writeoff) {

            $data =  OrderTimescardRecord::create([
                'order_id' => $orderid,
                'uid' => $uid,
                'yue_begin_time' => $time,
                'yue_end_time' => $time,
                'begin_time' => $time,
                'is_complete' => 1,
                'end_time' => $time,
                'create_day' => strtotime(time_ymd($time))
            ]);
            return $this->json(['data' => $data]);
        } else {
            return $this->json(['errno' => 1, 'message' => $message]);
        }
    }
}
