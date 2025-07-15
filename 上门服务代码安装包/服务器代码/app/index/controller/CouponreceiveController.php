<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\CouponReceive;
use app\model\Coupon;
use app\model\Goods;
use app\model\Category;

class CouponreceiveController extends Base
{
    public function submitorder()
    {

        $price = input('get.price', '', 'serach_in');
        $goods_id = input('get.goodsId', '', 'serach_in');
        $where['weid'] =  weid();
        $where['uid'] =   UID();

        $query = CouponReceive::where($where);
        $query->where(['is_use' => 0]);
        $query->where('end_time', '>=', time());
        if (!empty($price)) {
            $query->where('min_price', '<=', $price);
        }

        if(!empty($goods_id)){
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

        $data =  $query->select()->toArray();

        //$sql = $query->getLastsql();

        foreach ($data as &$cvo) {
            /*
            if ($cvo['expire_type'] == 10) {
                $cvo['end_time'] = strtotime("+" . $cvo['expire_day'] . " day", $cvo['create_time']);
                CouponReceive::where('id', $cvo['id'])->update(['end_time'=>$cvo['end_time']]);
            }*/

            if($cvo['use_goods']==1){
                $cvo['cat_ids_name'] = Category::getTitle($cvo['cat_ids']);
            }

            if($cvo['use_goods']==2){
                $cvo['goods_ids_name'] = Goods::getGoodsName($cvo['goods_ids']);
            }

            $cvo['reduce_price'] = number_format($cvo['reduce_price'], 0);
            $cvo['min_price'] = number_format($cvo['min_price'], 0);
            $cvo['start_time'] = time_ymd($cvo['start_time']);
            $cvo['end_time'] = time_ymd($cvo['end_time']);
        }

        return $this->json(['data' => $data, 'sql' => $sql]);
    }

    public function mylist()
    {
        $ptype = input('get.ptype', '', 'serach_in');
        $price = input('get.price', '', 'serach_in');
        $where['weid'] =  weid();
        $where['uid'] =   UID();

        $query = CouponReceive::where($where);
        if (!empty($price)) {
            $query->where('min_price', '<=', $price);
        }

        if (empty($ptype)) {
            $query->where(['is_use' => 0]);
        } elseif ($ptype == 1) {
            $query->where(['is_use' => 1]);
        } elseif ($ptype == 2) {
            $query->where('end_time', '<', time());
        }

        $data =  $query->select()->toArray();

        foreach ($data as &$cvo) {
            if($cvo['use_goods']==1){
                $cvo['cat_ids_name'] = Category::getTitle($cvo['cat_ids']);
            }

            if($cvo['use_goods']==2){
                $cvo['goods_ids_name'] = Goods::getGoodsName($cvo['goods_ids']);
            }

            $cvo['reduce_price'] = number_format($cvo['reduce_price'], 0);
            $cvo['min_price'] = number_format($cvo['min_price'], 0);
            $cvo['start_time'] = time_ymd($cvo['start_time']);
            $cvo['end_time'] = time_ymd($cvo['end_time']);
        }

        return $this->json(['data' => $data]);
    }

    //返回可领取的优惠券
    public function  couponlist()
    {
        global $_W;
        $errno = 0;
        $message = '返回消息';
        $uid = UID();

        $CouponReceive = CouponReceive::where(['uid' => $uid])->select()->toArray();
        $coupon_ids = array();

        for ($i = 0; $i < count($CouponReceive); $i++) {
            array_push($coupon_ids, $CouponReceive[$i]['coupon_id']);
        }
        $data = Coupon::where(['weid' => weid(), 'ptype' => 1])->select()->toArray();

        foreach ($data as &$cvo) {
            $cvo['reduce_price'] = number_format($cvo['reduce_price'], 0);
            $cvo['min_price'] = number_format($cvo['min_price'], 0);
            $cvo['start_time'] = time_ymd($cvo['start_time']);
            $cvo['end_time'] = time_ymd($cvo['end_time']);
            if (in_array($cvo['id'], $coupon_ids)) {
                $cvo['fetch'] = 1;
            }
            if($cvo['use_goods']==1){
                $cvo['cat_ids_name'] = Category::getTitle($cvo['cat_ids']);
            }

            if($cvo['use_goods']==2){
                $cvo['goods_ids_name'] = Goods::getGoodsName($cvo['goods_ids']);
            }
        }

        return $this->json(['data' => $data]);
    }
    public function add()
    {
        $id = input('post.coupon_id', '', 'serach_in');
        $Coupondata = Coupon::where(['weid' => weid(), 'id' => $id])->find();

        if (!empty($Coupondata)) {
            $Coupondata = $Coupondata->toArray();
            if ($Coupondata['total_num'] == -1 || $Coupondata['total_num'] > $Coupondata['receive_num']) {
                unset($Coupondata['id']);
                $Coupondata['uid'] = UID();
                $Coupondata['coupon_id'] = $id;

                $r = CouponReceive::create($Coupondata);

                if ($r) {
                    $message = "领取成功";
                    $receive_num = $Coupondata['receive_num'] + 1;

                    Coupon::update(['id' => $id, 'receive_num' => $receive_num]);
                } else {
                    $message = "领取失败";
                }
            } else {
                $message = "领取失败,你来晚了";
            }
        }

        return $this->json(['message' => $message, 'data' => $data]);
    }
    //领取优惠券
    public function fetch()
    {
        $errno = 0;
        $message = '返回消息';
        $uid = UID();

        $id = input('post.id', '', 'intval');

        $Coupondata = Coupon::where(['weid' => weid(), 'id' => $id])->find();
        $havecoupon = CouponReceive::where(['coupon_id' => $id, 'uid' => $uid])->find();

        if (empty($havecoupon)) {
            if (!empty($Coupondata)) {
                $Coupondata = $Coupondata->toArray();
                //领取优惠券前，判断发放数量
                if ($Coupondata['total_num'] == -1 || $Coupondata['total_num'] > $Coupondata['receive_num']) {

                    unset($Coupondata['id']);
                    $Coupondata['uid'] = UID() ? UID() : 0;
                    $Coupondata['coupon_id'] = $id;

                    if ($Coupondata['expire_type'] == 10) {
                        $Coupondata['end_time'] = strtotime("+" . $Coupondata['expire_day'] . " day");
                    }

                    unset($Coupondata['create_time']);
                    unset($Coupondata['update_time']);
                    $r = CouponReceive::create($Coupondata);

                    if ($r) {
                        $message = "领取成功";
                        //领取成功后更新优惠券领取数量
                        $receive_num = $Coupondata['receive_num'] + 1;
                        Coupon::update(['id' => $id, 'receive_num' => $receive_num]);
                    } else {
                        $message = "领取失败";
                    }
                } else {
                    $errno = '2001';
                    $message = '领取失败,你来晚了';
                }
            }
        } else {
            $errno = '2003';
            $message = '已经领取过了！';
        }
        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }
}
