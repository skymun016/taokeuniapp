<?php

namespace app\model;

use think\Model;

class Paymethod extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'paymethod';

   //points_pay  积分支付
   //balance_pay  余额支付
   //wx_pay 微信支付
   //offline_pay  线下支付
   //delivery_pay  delivery_pay
    public static function datainitial()
    {
        $weid = weid();
        $data[0] = [
            'weid' => $weid,
            'code' => 'balance_pay',
            'title' => '余额支付',
            'settings' => '',
            'collection_voucher' => 0,
            'group_ids' => '',
            'status' => 0,
            'sort' => 9,
        ];
        $data[1] = [
            'weid' => $weid,
            'code' => 'wx_pay',
            'title' => '微信支付',
            'settings' => '',
            'collection_voucher' => 0,
            'group_ids' => '',
            'status' => 0,
            'sort' => 10,
        ];
        $data[2] = [
            'weid' => $weid,
            'code' => 'offline_pay',
            'title' => '线下支付',
            'settings' => '',
            'collection_voucher' => 1,
            'group_ids' => '',
            'status' => 0,
            'sort' => 20,
        ];
        $data[3] = [
            'weid' => $weid,
            'code' => 'delivery_pay',
            'title' => '货到付款',
            'settings' => '',
            'collection_voucher' => 1,
            'group_ids' => '',
            'status' => 0,
            'sort' => 30,
        ];

        foreach ($data as $vo) {
            if (!(self::where(['weid' => $weid, 'code' => $vo['code']])->find())) {
                self::create($vo);
            }
        }
    }

    public static function getwx_settings()
    {
        $data = self::where(['weid' => weid(), 'code' => 'wx_pay'])->order('id desc')->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }

        $data['settings'] = iunserializer($data['settings']);

        return $data['settings'];
    }
}
