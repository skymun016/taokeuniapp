<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Cashregister;
use app\model\CashregisterLog;
use app\model\Goods;
use app\model\GoodsGiftcardType;
use app\model\OrderCard;
use app\model\CouponReceive;
use app\model\Category;
use app\model\Store;

class CashregisterController extends Base
{

    public function list()
    {
        $data = Cashregister::cartlist(['operator_id' => $this->userInfo['id']]);
        return $this->json(['data' => $data]);
    }

    public function delete()
    {
        $id = input('post.id', '', 'intval');
        $ids = input('post.ids', '', 'serach_in');
        if (!empty($id)) {
            $result = CashregisterLog::where('id', $id)->delete();
        } elseif (!empty($ids)) {
            $inids = explode(',', $ids);
            $result = CashregisterLog::where(['id' => $inids])->delete();
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
        $cart = new Cashregister;
        $param['goods_id'] = input('post.goodsId', '', 'serach_in');

        $param['operator_id'] = $this->userInfo['id'];

        $param['sku'] = input('post.sku');
        $param['quantity'] = input('post.quantity', '', 'serach_in');

        $param['weid'] = weid();

        //加入	
        if ($cart->add($param)) {
            $data = Cashregister::cartlist(['operator_id' => $param['operator_id']]);
        } else {
            $errno = 1;
            $message = '加入失败';
        }

        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }
    public function selectmember()
    {
        $uid = input('post.uid', '', 'serach_in');
        $cash = Cashregister::getcash(['operator_id' => (int) $this->userInfo['id']]);

        $res = Cashregister::where(['id' => $cash['id']])->update(['uid' => (int) $uid]);

        return $this->json(['data' => $res]);
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
        $quantity = input('post.quantity', '', 'serach_in');

        if (!empty($id)) {
            $cart = CashregisterLog::find($id);

            $sku = $cart->sku;
            $goodsid = $cart->goods_id;
            $goods = $goodsmob->cartGoods(['id' => $goodsid, 'sku' => $sku]);
            if (intval($quantity) < 1) {
                $quantity = intval($cart->quantity) + 1;
            }

            $result = CashregisterLog::update(['quantity' => $quantity, 'id' => $id]);
        }
        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }

    public function settledrawer()
    {

        $Cashregister = Cashregister::cartlist(['operator_id' => $this->userInfo['id']]);

        $uid = $Cashregister['member']['id'];

        if (!empty($this->sid)) {
            $sid = $this->sid;
        }

        if (!empty($this->tzid)) {
            $sid = Store::getidbytzid($this->tzid);
        }
        
        $price = $Cashregister['totalprice'];
        $weid = weid() ;


        $goods_id = input('get.goodsId', '', 'serach_in');

        if(count($Cashregister['shopList'])){
            
        }

       foreach($Cashregister['shopList'] as $vo){

       }

        $goodsgiftcard = [];
        $weid = weid();
        $Giftcardidswhere['weid'] =  $weid;
        $Giftcardidswhere['uid'] =   $uid;
        $Giftcardidswhere['sid'] =   $sid;
        $Giftcardidswhere['ptype'] =  3;
        $Giftcardids = GoodsGiftcardType::getidsbygoods($goods_id);

        if (!empty($Giftcardids)) {
            $query = OrderCard::where($Giftcardidswhere);
            //$query->where('end_time', '>=', time());
            $query->where('card_tid', 'in', $Giftcardids);

            $goodsgiftcard =  $query->select()->toArray();

            //$sql =  $query->getLastsql();
        }
        if (!empty($goodsgiftcard)) {
            foreach ($goodsgiftcard as &$vo) {
                $vo['minialias'] = substr($vo['order_num_alias'], -5);
                $vo['styleno'] = substr($vo['id'], -1);
                if ($vo['styleno'] > 5) {
                    $vo['styleno'] = $vo['styleno'] - 5;
                }
            }
        }
        $data['goodsgiftcard'] = $goodsgiftcard;


        $goods_id = input('get.goodsId', '', 'serach_in');


        $Couponwhere['weid'] = $weid;

        $query = CouponReceive::where($Couponwhere);
        $query->where(['is_use' => 0]);
        $query->where('end_time', '>=', time());
        if (!empty($price)) {
            $query->where('min_price', '<=', $price);
        }

        if (!empty($goods_id)) {
            $query->where(function ($q) use ($goods_id) {
                $Goods = Goods::find($goods_id);
                if (!empty($Goods)) {
                    $Goods = $Goods->toArray();
                    $parentIds = Category::getParentIdsstr($Goods['cat_id']);

                    $q->where('use_goods', 0)->whereOr('cat_ids', 'in', $parentIds)->whereOr('goods_ids', $goods_id);
                } else {
                    $q->where('use_goods', 999);
                }
            });
        }

        $Coupon =  $query->select()->toArray();

        //$sql = $query->getLastsql();

        foreach ($Coupon as &$cvo) {

            if ($cvo['use_goods'] == 1) {
                $cvo['cat_ids_name'] = Category::getTitle($cvo['cat_ids']);
            }

            if ($cvo['use_goods'] == 2) {
                $cvo['goods_ids_name'] = Goods::getGoodsName($cvo['goods_ids']);
            }

            $cvo['reduce_price'] = number_format($cvo['reduce_price'], 0);
            $cvo['min_price'] = number_format($cvo['min_price'], 0);
            $cvo['start_time'] = time_ymd($cvo['start_time']);
            $cvo['end_time'] = time_ymd($cvo['end_time']);
        }

        $data['Coupon'] = $Coupon;

        return $this->json(['data' => $data, 'sql' => $sql,]);
    }
}
