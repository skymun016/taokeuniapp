<?php

namespace app\model;

use think\Model;

class Cashregister extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'cashregister';

    public function cashregisterLog()
    {
        return $this->hasMany(CashregisterLog::class, 'cid');
    }

    public function add($data = array())
    {

        $weid = weid();
        if (empty($data)) {
            return false;
        }

        $Cashrmodel = self::getcash(['operator_id' => (int) $data['operator_id']]);

        if ($Cashrmodel['id']) {
            $updatedata['cid'] = $Cashrmodel['id'];

            $updatedata['goods_id'] = (int) $data['goods_id'];
            $updatedata['quantity'] = (int) $data['quantity'];

            if (isset($data['type'])) {
                $updatedata['type'] = $data['type'];
            }

            $updatedata['sku'] = $data['sku'];

            $where['cid'] = $Cashrmodel['id'];
            $where['goods_id'] = $updatedata['goods_id'];
            $where['sku'] = $updatedata['sku'];

            $Logmodel = CashregisterLog::where($where)->find();

            if ($Logmodel) {
                $Logmodel = $Logmodel->toArray();
                $quantity = (int) $Logmodel['quantity'] + (int) $updatedata['quantity'];
                return CashregisterLog::where('id', $Logmodel['id'])->update(['quantity' => $quantity]);
            } else {
                return CashregisterLog::create($updatedata);
            }
        }
    }

    static public function getcash($where)
    {
        $where['weid'] = weid();
        $cash = self::where($where)->order('id desc')->find();

        if (empty($cash)) {
            $cash = self::create([
                'weid' => $where['weid'],
                'operator_id' => (int) $where['operator_id'],
            ]);
        }

        if ($cash) {
            $cash = $cash->toArray();
        }
        return $cash;
    }

    static public function cartlist($where)
    {
        $cash = self::getcash($where);
        if ($cash) {
            $listwhere['cid'] = $cash['id'];
            $cartlist = CashregisterLog::where($listwhere)->select()->toArray();
            return self::carttotal($cartlist, $cash);
        }
    }

    static public function dellog($where)
    {
        $where['weid'] = weid();
        $cash = self::where($where)->order('id desc')->find();

        if ($cash) {
            $listwhere['cid'] = $cash->id;
            self::where(['id' => $cash->id])->delete();
            CashregisterLog::where($listwhere)->delete();
        }
    }

    static public function totalprice($data)
    {
        $amountTotle = 0;   //商品总价
        if (!empty($data)) {
            foreach ($data as $vo) {
                $amountTotle = ($amountTotle + $vo['total']);
            }
        }
        return round($amountTotle, 2);
    }

    static public function totalPayPoints($data)
    {
        $res['points'] = 0;   //总积份
        $res['points_price'] = 0;   //能抵扣的金额
        if (!empty($data)) {
            foreach ($data as $vo) {
                if ($vo['pay_points'] > 0) {
                    $res['points'] = ($res['points'] + $vo['pay_points']);
                    $$res['points_price'] = ($res['points_price'] + $vo['points_price']);
                }
            }
        }
        return $res;
    }

    //计算购物车商品数量sam
    static public function carttotal($cartlist, $cash = [])
    {
        $goodsmob = new Goods;
        $weight = 0;
        foreach ($cartlist as $vo) {
            $goods = $goodsmob->cartGoods([
                'id' => $vo['goods_id'],
                'msid' => $vo['msid'],
                'tuanid' => $vo['tuanid'],
                'sku' => $vo['sku'],
                'quantity' => $vo['quantity'],
                'is_skumore' => $vo['is_skumore'],
                'skumore' => $vo['skumore']
            ]);

            if (!empty($goods['cat_id'])) {

                $category = Category::find($goods['cat_id']);
                if (!empty($category)) {
                    $goods['category'] = $category->toArray();
                }
            }
            if (empty($goods['category']['deliverymode'])) {
                $goods['category']['deliverymode'] = 3;
            }

            $goods['deliverymode'] = $goods['category']['deliverymode'];

            //加空判定
            if (!empty($goods['deliverymode'])) {
                $deliverymodearray = explode(',', $goods['deliverymode']);
            }

            if ($deliverymodearray[1]) {
                $goods['deliverymode'] = $deliverymodearray[0];
                if ($goods['category']['ptype'] == 1) {
                    $goods['deliverymodearray'] = getgoodsdeliverymodearray($goods['category']['deliverymode']);
                } elseif ($goods['category']['ptype'] == 2) {
                    $goods['deliverymodearray'] = getservicedeliverymodearray($goods['category']['deliverymode']);
                }
            } else {
                $goods['deliverymodearray'] = [];
            }

            $goods['active'] = true;
            $goods['cart_id'] = $vo['id'];
            $goods['goodsId'] = $vo['goods_id'];
            $goods['sku'] = $vo['sku'];
            //$goods['label'] = $label;
            $goods['left'] = "margin-left:0px";
            $goods['number'] = $vo['quantity'];
            $goods['pic'] = toimg($goods['image']);
            if (!empty($goods['shipping'])) {
                $weightisnull = 0.1;
                $weight += $goods['weight'];
            }

            $shopList[] = $goods;
            //$label = "";
            if ($goods['is_additional']) {
                $data['is_additional'] = $goods['is_additional'];
            }
        }
        $data['shopList'] = $shopList;
        if (empty($weight)) {
            $data['weight'] = $weightisnull;
        } else {
            $data['weight'] = $weight;
        }
        $data['totalprice'] = self::totalprice($shopList);
        $totalPoints = self::totalPayPoints($shopList);
        $data['totalPayPoints'] = $totalPoints['points'];
        if ($cash) {
            if ($cash['uid']) {
                $member = Member::find($cash['uid']);
                if ($member) {
                    $data['member'] = $member->toArray();
                }
            }
        }
        $data['totalPointsPrice'] = $totalPoints['points_price'];
        return $data;
    }
}
