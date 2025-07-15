<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\OrderCard;
use app\model\GoodsGiftcardType;

class OrdercardController extends Base
{
    public function goodsgiftcard()
    {
        $sid = input('get.sid', '', 'serach_in');
        $goods_id = input('get.goodsId', '', 'serach_in');
        $price = input('get.price', '', 'serach_in');
        $data = [];
        $weid = weid();
        $where['weid'] =  $weid;
        $where['uid'] =   UID();
        $where['sid'] =   $sid;
        $where['ptype'] =  3;
        $Giftcardids = GoodsGiftcardType::getidsbygoods($goods_id);

        if (!empty($Giftcardids)) {
            $query = OrderCard::where($where);
            //$query->where('end_time', '>=', time());
            $query->where('card_tid', 'in', $Giftcardids);

            $data =  $query->select()->toArray();

            //$sql =  $query->getLastsql();
        }
        if (!empty($data)) {
            foreach ($data as &$vo) {
                $vo['minialias'] = substr($vo['order_num_alias'], -5);
                $vo['styleno'] = substr($vo['id'], -1);
                if ($vo['styleno'] > 5) {
                    $vo['styleno'] = $vo['styleno'] - 5;
                }
            }
        }
        return $this->json(['data' => $data, 'sql' => $sql,]);
    }
}
