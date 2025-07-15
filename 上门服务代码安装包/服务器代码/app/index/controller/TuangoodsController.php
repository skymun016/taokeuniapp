<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Goods;
use app\model\TuanGoods;
use app\model\GoodsSku;
use app\model\GoodsSkuValue;
use app\model\GoodsImage;

class TuangoodsController extends Base
{
    public function index()
    {
        $weid = weid();
        $page = input('post.page', 1, 'intval');
        $keyword = input('post.keyword', '', 'serach_in');
        $ocid = input('post.ocid', '', 'serach_in');

        if (empty($ocid)) {
            $ocid = $this->ocid;
        }

        $query = TuanGoods::where(['weid' => $weid, 'status' => 1]);

        $priceOrder = input('post.priceOrder', '', 'serach_in');
        $salesOrder = input('post.salesOrder', '', 'serach_in');

        if (!empty($priceOrder)) {
            $Sort = 'price ' . $priceOrder;
        } elseif (!empty($salesOrder)) {
            $Sort = 'sale_count ' . $salesOrder;
        } else {
            $Sort = 'sort asc,id desc';
        }

        if (!empty($ocid)) {
            $query->where('ocid', $ocid);
        }

        if (!empty($keyword)) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        $res = $query->order($Sort)
            ->paginate(getpage())
            ->toArray();

        $res['data'] = TuanGoods::setGoodslist($res['data']);

        $data['data'] = $res;

        return $this->json($data);
    }
}
