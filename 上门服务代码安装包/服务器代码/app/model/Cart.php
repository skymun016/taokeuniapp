<?php

namespace app\model;

use think\Model;

class Cart extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'cart';

    /**
     * 加入购物车
     * @param uid 	       用户id
     * @param goods_id  商品id
     * @param quantity  商品数量 
     * @param sku    商品选项 
     */
    public function add($data = array())
    {

        if (empty($data)) {
            return false;
        }

        $cart['uid'] = $data['uid'];
        $cart['weid'] = $data['weid'];

        $cart['goods_id'] = (int) $data['goods_id'];
        $cart['quantity'] = (int) $data['quantity'];

        if (isset($data['type'])) {
            $cart['type'] = $data['type'];
        }

        $cart['sku'] = $data['sku'];

        $where['uid'] = $cart['uid'];
        $where['weid'] = $cart['weid'];
        $where['type'] = $cart['type'];
        $where['goods_id'] = $cart['goods_id'];
        $where['sku'] = $cart['sku'];

        //判断是否有同一个商品
        $cartmodel = Cart::where($where)->find();

        if ($cartmodel) {
            $cartmodel = $cartmodel->toArray();
            $quantity = (int) $cartmodel['quantity'] + (int) $cart['quantity'];
            return Cart::where('id', $cartmodel['id'])->update(['quantity' => $quantity]);
        } else {
            return Cart::create($cart);
        }
    }

    public function getlist($type = 'money')
    {
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();
        $where['uid'] = $memberinfo['id'];
        $where['type'] = $type;
        $cartlist = $this->cartlist($where);
        return $this->carttotal($cartlist, $type);
    }

    public function getlistbyid($id, $type = 'money')
    {
        $where['id'] = explode(',', $id);
        $where['type'] = $type;
        $cartlist = $this->cartlist($where);
        return $this->carttotal($cartlist, $type);
    }

    public function cartlist($where)
    {
        $cartlist = $this::where($where)->select()->toArray();
        return $cartlist;
    }

    public function totalprice($data)
    {
        $amountTotle = 0;   //商品总价
        if (!empty($data)) {
            foreach ($data as $vo) {
                $amountTotle = ($amountTotle + $vo['total']);
            }
        }
        return $amountTotle;
    }

    public function totalPayPoints($data)
    {
        $res['points'] = 0;   //总积份
        $res['points_price'] = 0;   //能抵扣的金额
        if (!empty($data)) {
            foreach ($data as $vo) {
                if($vo['pay_points']>0){
                    $res['points'] = ($res['points'] + $vo['pay_points']);
                    $$res['points_price'] = ($res['points_price'] + $vo['points_price']);
                }
            }
        }
        return $res;
    }

    //计算购物车商品数量sam
    public function carttotal($cartlist, $type = 'money')
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
        if (!empty($cartlist['uid'])) {
            $data['shopNum'] = $this->count_cart_total($type);
        }
        if (empty($weight)) {
            $data['weight'] = $weightisnull;
        } else {
            $data['weight'] = $weight;
        }
        $data['totalprice'] = $this->totalprice($shopList);
        $totalPoints = $this->totalPayPoints($shopList);
        $data['totalPayPoints'] = $totalPoints['points'];
        $data['totalPointsPrice'] = $totalPoints['points_price'];
        return $data;
    }

    //计算购物车商品数量
    public function count_cart_total($type = 'money')
    {

        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();

        $total = $this::where(array('uid' => $memberinfo['id'], 'type' => $type))->sum('quantity');
        return $total;
    }
}
