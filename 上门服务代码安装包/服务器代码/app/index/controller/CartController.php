<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Cart;
use app\model\Member;
use app\model\Goods;

class CartController extends Base
{
    public function list()
    {
        $cart = new Cart;
        $data = $cart->getlist();
        return $this->json(['data' => $data]);
    }

    public function total()
    {
        $cart = new Cart;
        $total = $cart->count_cart_total();
        $data['total'] = $total;
        return $this->json(['data' => $data]);
    }

    public function del()
    {
        $id = input('post.id', '', 'intval');
        $ids = input('post.ids', '', 'serach_in');
        if (!empty($id)) {
            $result = Cart::where('id', $id)->delete();
        } elseif (!empty($ids)) {
            $inids = explode(',', $ids);
            Cart::where(['id' => $inids])->delete();
        }
        if ($result) {
            $message = '删除成功';
        } else {
            $message = '删除失败';
        }
        return $this->json(['message' => $message, 'data' => $data]);
    }

    public function add()
    {

        $cart = new Cart;
        $param['goods_id'] = input('post.goodsId', '', 'serach_in');
        $param['sku'] = input('post.sku');
        $param['quantity'] = input('post.quantity', '', 'serach_in');

        if (empty($param['type'])) {
            $param['type'] = 'money';
        }

        $Membermob = new Member;

        $memberinfo = $Membermob->getUserByWechat();

        $param['uid'] = $memberinfo['id'];

        $param['weid'] = weid();

        //加入购物车	
        if ($cart->add($param)) {
            //计算购物车商品数量
            $total = $cart->count_cart_total();
        } else {
            $errno = 1;
            $message = '更新失败';
        }

        $data['total'] = $total;

        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }

    public function check()
    {
        $goodsmob = new Goods;
        $id = input('post.goodsId', '', 'serach_in');
        $sku = input('post.sku');
        $number = input('post.number', '', 'serach_in');

        $goodsPrice = $goodsmob->cartGoods(['id' => $id, 'sku' => $sku, 'quantity' => $number]);

        $data['price'] = $goodsPrice['price'];
        $data['total'] = $goodsPrice['total'];
        $data['points'] = $goodsPrice['total_return_points'];
        $data['stores'] = $goodsPrice['stores'];
        $data['sku'] = $goodsPrice['sku'];

        return $this->json(['data' => $data]);
    }

    public function quantity()
    {
        $errno = 0;
        $goodsmob = new Goods;
        $id = input('post.id', '', 'intval');
        $uptype = input('post.uptype', '', 'serach_in');
        $quantity = input('post.quantity', '', 'serach_in');

        if ($uptype == 'plus') {
            $uptype =  'jia';
        }
        if ($uptype == 'reduce') {
            $uptype =  'jian';
        }

        if (!empty($id)) {
            $cart = Cart::find($id);
            if ($uptype == 'jia') {
                $sku = $cart->sku;
                $goodsid = $cart->goods_id;
                $goods = $goodsmob->cartGoods(['id' => $goodsid, 'sku' => $sku]);
                if (intval($quantity) < 1) {
                    $quantity = intval($cart->quantity) + 1;
                }
                if (intval($quantity) > intval($goods['stores'])) {
                    $errno = 1;
                    $message = '商品库不足';

                    return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
                }
            } elseif ($uptype == 'jian') {

                if (intval($quantity) < 1) {
                    $quantity = intval($cart->quantity) - 1;
                }
                if (intval(jian) < 1) {
                    //数量小于1删除商品
                    Cart::where('id', $id)->delete();
                }
            }
            $result = Cart::update(['quantity' => $quantity, 'id' => $id]);
        }
        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }
}
