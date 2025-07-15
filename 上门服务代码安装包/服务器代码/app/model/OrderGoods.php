<?php

namespace app\model;

use think\Model;

class OrderGoods extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order_goods';

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }

    function getOrderGoods($order_id)
    {
        $data = $this::where(['order_id' => $order_id])->select()->toArray();

        foreach ($data as &$vo) {
            if (empty($vo["image"])) {
                $goods = Goods::where(['id' => $vo['goods_id']])->find();
                if (!empty($goods)) {
                    $goods = $goods->toArray();
                }

                if (!empty($goods)) {
                    $vo['pic'] = toimg($goods['image']);
                    $vo['image'] = toimg($goods['image']);
                }
            } else {
                $vo['image'] = toimg($vo['image']);
                $vo['pic'] = toimg($vo['image']);
            }
            $vo['label'] = $vo['sku'];
        }
        return $data;
    }

    public static function getOrderGoodsids($order_id)
    {
        $data = OrderGoods::where(['order_id' => $order_id])->select()->toArray();
        $ids = '';
        foreach ($data as &$vo) {
            if ($vo['goods_id'] > 0) {
                if ($ids == '') {
                    $ids = $vo['goods_id'];
                } else {
                    $ids = $ids . ',' . $vo['goods_id'];
                }
            }
        }
        return $ids;
    }

    public static function getCommission($order_info, $roletype, $percent)
    {
        $total = 0.00;
        $OrderGoods = OrderGoods::where(['order_id' => $order_info['id']])
            ->select()
            ->toArray();
        foreach ($OrderGoods as $vo) {
            if (!empty($vo['goods_id'])) {
                if ($vo['card_tid'] > 0 && !empty($roletype)) {
                    $GiftcardCommission = GoodsGiftcardCommission::where('card_tid', $vo['card_tid'])->where('roletype', $roletype)->find();
                    if (!empty($GiftcardCommission)) {
                        $GiftcardCommission = $GiftcardCommission->toArray();
                        if ($GiftcardCommission['commission_method'] == 0) {
                            $total = $total + (($vo['total'] * percent_to_num($GiftcardCommission['return_percent'])));
                        } elseif ($GiftcardCommission['commission_method'] == 1) {
                            $total = $total + (($GiftcardCommission['return_percent'] * $vo['quantity']));
                        }
                    }
                } elseif ($vo['is_commission'] == 1) {
                    if ($vo['commission_price'] > 0) {
                        if ($vo['commission_method'] == 0) {
                            $total = $total + (($vo['total'] * percent_to_num($vo['commission_price'])) * percent_to_num($percent));
                        } elseif ($vo['commission_method'] == 1) {
                            $total = $total + (($vo['commission_price'] * $vo['quantity']) * percent_to_num($percent));
                        }
                    }
                } else {
                    $total += ($vo['total'] * percent_to_num($percent));
                }
            }
        }

        if ($order_info['is_additional'] == 1 && $order_info['additional_pay_time'] > 0) {
            $total = $total + ($order_info['additional'] * percent_to_num($percent));
        }

        return $total;
    }
}
