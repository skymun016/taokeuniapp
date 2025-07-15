<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Cart;
use app\model\Order;
use app\model\OrderCount;
use app\model\Member;
use app\model\Config;
use app\model\OrderStatus;
use app\model\OrderHistory;
use app\model\Goods;
use app\model\TuanFound;
use app\model\Paymethod;
use app\model\OrderStaff;
use app\model\OrderTuanzhang;
use app\model\Address;
use app\model\Category;
use app\model\TransportExtend;
use app\model\UuidRelation;
use app\model\Technical;
use app\model\Agent;
use app\model\RegisterField;
use app\model\Store;
use app\model\Operatingcity;
use app\model\Incomelog;
use app\model\Geocoder;
use app\model\OrderImage;
use app\model\OrderAddress;
use app\model\Users;
use app\model\Area;
use app\model\MessageMini;
use app\model\MessageMp;
use app\model\OrderCard;
use app\model\OrderTimescard;
use app\model\OrderTimescardRecord;

class OrderController extends Base
{

    public function list()
    {
        $uuid = UuidRelation::getuuid(UID(), 'technical');
        $uid = UID();
        $weid = weid();
        $status = input('post.status', '', 'serach_in');
        $admin = input('post.admin', '', 'serach_in');
        $technical = input('post.technical', '', 'serach_in');
        $publicorder = input('post.publicorder', '', 'serach_in');
        $store = input('post.store', '', 'serach_in');
        $ptype = input('post.ptype', '', 'serach_in');
        $is_times = input('post.is_times', '', 'serach_in');
        if (empty($ptype)) {
            $ptype = 1;
        }

        $query = Order::where(['weid' => $weid, 'ptype' => $ptype]);
        $query->with(['paymethod']);

        $Membermob = new Member;
        $Member = $Membermob->getUserByWechat();

        if ($admin == 1) {
        } elseif ($store == 1) {
            $data = Store::where(['uid' => UID()])->find();
            if ($data) {
                $data = $data->toArray();
                $query->where('sid', $data['id']);
            }
        } elseif ($technical == 1) {

            if (!empty($uuid) && !empty($uid)) {
                OrderStaff::where('uid', $uid)->update(['uuid' => $uuid]);
            }

            $withJoin = [
                'staff' => ['order_id', 'uuid', 'title', 'end_time', 'begin_time'],
            ];
            $query->withJoin($withJoin, 'left');
            $query->where(['staff.uuid' => $uuid]);
        } elseif ($publicorder == 1) {
            return $this->publicorder();
        } else {
            $query->where(['uid' => $Member['id']]);
        }

        if (!empty($is_times)) {
            $query->where('is_times', $is_times);
        }

        if (!empty($status)) {
            $query->where('order_status_id', $status);
        }

        $orderList = $query->order('id desc')->select()->toArray();

        //var_dump($query->getLastsql());

        $orderList =  Order::setOrderList($orderList);

        $data["orderList"] = $orderList;
        //$data["amountLogistics"] = $amountLogistics;
        $data["isNeedLogistics"] = 1;

        $data['is_agent'] = Agent::is_agent($Member['id']);

        return $this->json(['data' => $data]);
    }
    public function myorder()
    {
        $weid = weid();
        $ptype = input('post.ptype', '', 'serach_in');
        $status = input('post.status', '', 'serach_in');
        $currentTab = input('post.currentTab', '', 'serach_in');
        if (empty($status) && !empty($currentTab)) {
            $OrderStatus = OrderStatus::listname($ptype);
            $status = $OrderStatus[$currentTab]['id'];
        }

        $ispoints = input('post.ispoints', '', 'serach_in');
        $is_times = input('post.is_times', '', 'serach_in');
        if (empty($ptype)) {
            $ptype = 1;
        }

        $query = Order::where(['weid' => $weid, 'ptype' => $ptype]);
        $query->with(['paymethod']);

        $Membermob = new Member;
        $Member = $Membermob->getUserByWechat();

        $query->where(['uid' => $Member['id']]);

        if (!empty($ispoints)) {
            $query->where('payment_code', 'points_pay');
        }
        if (empty($is_times)) {
            $query->where('is_times', 0);
        } else {
            $query->where('is_times', '>', 0);
        }

        if (!empty($status)) {
            $query->where('order_status_id', $status);
        }

        OrderCount::upread($Member['id'], $status);

        $res = $query->order('id desc')
            ->paginate(getpage())
            ->toArray();
        //var_dump($query->getLastsql());
        $res['data'] = Order::setOrderList($res['data']);
        $data['data'] = $res;

        return $this->json($data);
    }
    public function technicalorder()
    {

        $Technical = Technical::getInfobyuid(UID());

        if ($Technical['status'] != 1) {
            throw new ValidateException("您不是师傅！");
        }

        $uuid = UuidRelation::getuuid(UID(), 'technical');
        $uid = UID();
        $weid = weid();
        $ptype = input('post.ptype', '', 'serach_in');
        $is_times = input('post.is_times', '', 'serach_in');
        $status = input('post.status', '', 'serach_in');
        $currentTab = input('post.currentTab', '', 'serach_in');
        if (empty($status) && !empty($currentTab)) {
            $OrderStatus = OrderStatus::listname($ptype);
            $status = $OrderStatus[$currentTab]['id'];
        }

        if (empty($ptype)) {
            $ptype = 2;
        }
        $query = Order::where(['weid' => $weid, 'ptype' => $ptype]);
        $query->where('is_times', (int)$is_times);
        if (!empty($status)) {
            $query->where('order_status_id', $status);
        }

        if (!empty($uuid) && !empty($uid)) {
            OrderStaff::where('uid', $uid)->update(['uuid' => $uuid]);
        }
        $withJoin = [
            'staff' => ['order_id', 'uuid', 'title', 'end_time', 'begin_time'],
        ];
        $query->withJoin($withJoin, 'left');
        $query->where(['staff.uuid' => $uuid]);

        $res = $query->order('id desc')
            ->paginate(getpage())
            ->toArray();

        $res['data'] = Order::setOrderList($res['data']);
        $data['data'] = $res;

        return $this->json($data);
    }

    public function storeorder()
    {

        $Store = Store::getInfobyuid(UID());

        if ($Store['status'] != 1) {
            throw new ValidateException("您不是商家！");
        }

        $weid = weid();
        $ptype = input('post.ptype', '', 'serach_in');
        $is_times = input('post.is_times', '', 'serach_in');
        $status = input('post.status', '', 'serach_in');
        $currentTab = input('post.currentTab', '', 'serach_in');
        if (empty($status) && !empty($currentTab)) {
            $OrderStatus = OrderStatus::listname($ptype);
            $status = $OrderStatus[$currentTab]['id'];
        }

        if (empty($ptype)) {
            $ptype = 2;
        }
        $query = Order::where(['weid' => $weid, 'ptype' => $ptype]);
        $query->where('is_times', (int)$is_times);
        if (!empty($status)) {
            $query->where('order_status_id', $status);
        }

        if ($Store) {
            $query->where('sid', $Store['id']);
        }

        $res = $query->order('id desc')
            ->paginate(getpage())
            ->toArray();


        $res['data'] = Order::setOrderList($res['data']);
        $data['data'] = $res;

        return $this->json($data);
    }

    public function operatingcityorder()
    {

        $Operatingcity = Operatingcity::getInfobyuid(UID());
        if ($Operatingcity['status'] != 1) {
            throw new ValidateException("您不是城市代码！");
        }

        $weid = weid();
        $ptype = input('post.ptype', '', 'serach_in');
        $status = input('post.status', '', 'serach_in');
        $currentTab = input('post.currentTab', '', 'serach_in');
        if (empty($status) && !empty($currentTab)) {
            $OrderStatus = OrderStatus::listname($ptype);
            $status = $OrderStatus[$currentTab]['id'];
        }

        if (empty($ptype)) {
            $ptype = 2;
        }
        $query = Order::where(['weid' => $weid, 'ptype' => $ptype]);
        if (!empty($status)) {
            $query->where('order_status_id', $status);
        }

        $uuid = UuidRelation::getuuid(UID(), 'operatingcity');

        $Operatingcitydata = Operatingcity::where(['uuid' => $uuid])->find();

        if ($Operatingcitydata) {
            $Operatingcitydata = $Operatingcitydata->toArray();
            if (empty($Operatingcitydata['areatype'])) {
                $Operatingcitydata['areatype'] = 3;
            }

            if ($Operatingcitydata['areatype'] == 3) {
                $query->where('shipping_district_name', $Operatingcitydata['district_name']);
            } elseif ($Operatingcitydata['areatype'] == 2) {
                $query->where('shipping_city_name', $Operatingcitydata['city_name']);
            } elseif ($Operatingcitydata['areatype'] == 1) {
                $query->where('shipping_province_name', 'like', '%' . $Operatingcitydata['province_name'] . '%' );
            }
        }

        $res = $query->order('id desc')
            ->paginate(getpage())
            ->toArray();


        $res['data'] = Order::setOrderList($res['data']);
        $data['data'] = $res;

        return $this->json($data);
    }

    public function adminorder()
    {

        $user = Users::getadminbyopenid();
        if (!$user) {
            throw new ValidateException("您不是管理员！");
        }

        $weid = weid();
        $ptype = input('post.ptype', '', 'serach_in');
        $status = input('post.status', '', 'serach_in');
        $currentTab = input('post.currentTab', '', 'serach_in');
        if (empty($status) && !empty($currentTab)) {
            $OrderStatus = OrderStatus::listname($ptype);
            $status = $OrderStatus[$currentTab]['id'];
        }

        if (empty($ptype)) {
            $ptype = 2;
        }
        $query = Order::where(['weid' => $weid, 'ptype' => $ptype]);
        if (!empty($status)) {
            $query->where('order_status_id', $status);
        }

        $res = $query->order('id desc')
            ->paginate(getpage())
            ->toArray();


        $res['data'] = Order::setOrderList($res['data']);
        $data['data'] = $res;

        return $this->json($data);
    }

    public function teamorder()
    {

        $weid = weid();
        $status = input('post.status', '', 'serach_in');
        $ptype = input('post.ptype', '', 'serach_in');

        $Membermob = new Member;
        $Member = $Membermob->getUserByWechat();

        $where['weid'] = $weid;
        $where['uid'] = $Member['id'];

        $orderids = Incomelog::where($where)->column('order_id');

        $query = Order::where(['weid' => $weid]);

        if (!empty($ptype)) {
            $query->where('ptype', $ptype);
        }

        if (!empty($status)) {
            $query->where('order_status_id', $status);
        }

        $query->where('id', 'in', $orderids);
        //$uidlist = Member::getDepart($Member['id']);
        //$query->where(['uid' => $uidlist]);

        $res = $query->order('id desc')
            ->paginate(getpage())
            ->toArray();

        $res['data'] = Order::setOrderList($res['data']);
        $data['data'] = $res;

        return $this->json($data);
    }

    public function publicorder()
    {
        $identity = input('post.identity', '', 'serach_in');

        $from['longitude'] = input('post.longitude', '', 'serach_in');
        $from['latitude'] = input('post.latitude', '', 'serach_in');

        $weid = weid();
        $ptype = input('post.ptype', '', 'serach_in');
        if (empty($ptype)) {
            $ptype = 2;
        }
        $query = Order::where(['weid' => $weid, 'ptype' => $ptype]);
        $uuid = UuidRelation::getuuid(UID(), $identity);

        if ($identity == 'technical') {

            $technicalConfig = Config::getconfig('technical');
            if ($technicalConfig['is_pickuporder'] != 1) {
                return $this->json(['errno' => 1, 'msg' => '系统没有开启抢单功能！']);
            }

            $Technicaldata = Technical::where('uuid', $uuid)->find();
            if (!empty($Technicaldata)) {
                $Configdata = Config::getconfig();

                if (empty($Configdata['locationscope'])) {
                    $Configdata['locationscope'] = 3;
                }
                if ($Configdata['locationscope'] == 3 && $Technicaldata->district_name) {
                    $city = $Technicaldata->district_name;
                    $query->where(['shipping_district_name' => $Technicaldata->district_name]);
                } else if ($Configdata['locationscope'] == 2 && $Technicaldata->city_name) {
                    $city = $Technicaldata->city_name;
                    $query->where(['shipping_city_name' => $Technicaldata->city_name]);
                } else if ($Configdata['locationscope'] == 1 && $Technicaldata->province_name) {
                    $city = $Technicaldata->province_name;
                    $query->where('shipping_province_name', 'like', '%' . $Technicaldata->province_name . '%' );
                }
            }

            if (empty($city)) {
                $query->where(['shipping_city_name' => '-1']);
            }
        }

        $query->where(['order_status_id' => 2]);

        if ($identity == 'technical') {
            if ($Technicaldata->cate_ids) {
                $query->where(['order_status_id' => 2]);
                $query->where('cate_ids', 'in', Category::getidssonid($Technicaldata->cate_ids));
            }
        }

        $res = $query->order('id desc')
            ->paginate(getpage())
            ->toArray();
        $sql = $query->getLastsql();
        //var_dump($query->getLastsql());

        $res['data'] = Order::setOrderList($res['data']);
        if ($from['longitude'] && $from['latitude']) {
            foreach ($res['data'] as &$vo) {

                $addressdata = OrderAddress::where(['order_id' => $vo['id'], 'ptype' => 1])->find();

                if (empty($addressdata)) {
                    $addressdata = Address::find($vo['address_id']);
                }

                if (!empty($addressdata)) {
                    $addressdata = $addressdata->toArray();
                    $to1['longitude'] = $addressdata['longitude'];
                    $to1['latitude'] = $addressdata['latitude'];
                    if ($to1['longitude'] && $to1['latitude']) {
                        $vo['realdistance'] =  Geocoder::get_distance($from, $to1);
                    }
                }

                if ($vo['is_errands'] == 1) {
                    $takeaddressdata = OrderAddress::where(['order_id' => $vo['id'], 'ptype' => 2])->find();

                    if (empty($takeaddressdata)) {
                        $takeaddressdata = Address::find($vo['take_address_id']);
                    }

                    if (!empty($takeaddressdata)) {
                        $takeaddressdata = $takeaddressdata->toArray();
                        $to2['longitude'] = $takeaddressdata['longitude'];
                        $to2['latitude'] = $takeaddressdata['latitude'];
                        if ($to2['longitude'] && $to2['latitude']) {
                            $vo['takerealdistance'] =  Geocoder::get_distance($from, $to2);
                        }
                    }
                }
            }
        }

        $data['data'] = $res;

        //兼容旧版
        $data['data']["orderList"] = $res['data'];
        $data['data']["sql"] = $sql;

        return $this->json($data);
    }

    public function detail()
    {
        $id = input('get.id', '', 'serach_in');
        $data = Order::order_info($id);

        if (!empty($data['orderInfo']['cat_id'])) {
            $data['orderInfo']['cateMap']['image'] = Category::getImage($data['orderInfo']['cat_id']);
        } else {
            foreach ($data['goods'] as &$vo) {
                $vo['goods']['image'] = toimg($vo['goods']['image']);
            }
        }
        if ($data['orderInfo']['additional'] > 0 && $data['orderInfo']['additional_pay_time'] == 0) {
            $tmplIds = Config::getconfig('subscribemessage');
            $data['tmplIds'][] = $tmplIds['complete_tpl'];
        }

        if ($data['orderInfo']['is_errands'] == 1) {

            $takeaddressdata = OrderAddress::where(['order_id' => $id, 'ptype' => 2])->find();

            if (empty($takeaddressdata)) {
                $takeaddressdata = Address::find($data['orderInfo']['take_address_id']);
            }

            if (!empty($takeaddressdata)) {
                $takeaddressdata = $takeaddressdata->toArray();

                $data['orderInfo']['take_address'] = $takeaddressdata;
            }
        }

        return $this->json(['data' => $data]);
    }
    public function countdown()
    {
        $id = input('get.id', '', 'serach_in');
        $Orderdata = Order::order_info($id);

        $data['Orderdata'] = $Orderdata;
        $data['time_1'] = floor((time() - $Orderdata['orderInfo']['start_time']) / 60);
        $data['start_time'] = date('Y-m-d H:i:s', $Orderdata['orderInfo']['start_time']);
        $data['time_amount'] = ($Orderdata['goods'][0]['time_amount'] * 60) - (time() - $Orderdata['orderInfo']['start_time']);

        return $this->json(['data' => $data]);
    }
    public function errandscreate()
    {
        $ordermod = new Order;
        $payment = input('post.paymentType', '', 'serach_in');

        $address_id = input('post.address_id', '', 'serach_in');
        $take_address_id = input('post.take_address_id', '', 'serach_in');

        if (empty($address_id)) {
            return $this->json(['errno' => 1, 'msg' => '请填写收件地址']);
        }
        if (empty($take_address_id)) {
            return $this->json(['errno' => 1, 'msg' => '请填写取件地址']);
        }

        $remark = input('post.remark', '', 'serach_in');
        $distance = input('post.distance', '', 'serach_in');
        $amountTotle = input('post.amountTotle', '', 'serach_in');

        $order['distance'] = $distance;
        $order['total'] = $amountTotle;
        $order['number'] = 1;

        //支付方式
        if (!empty($payment)) {
            $order['payment_code'] = $payment;

            $paymentdata = Paymethod::where(['code' => $payment, 'weid' => weid()])->find();

            $order['pay_method_id'] = $paymentdata->id;
        }

        $order['address_id'] = $address_id;
        $order['take_address_id'] = $take_address_id;
        $order['remark'] = $remark;
        $order['uid'] = UID();

        $ordermod = new Order;
        $data = $ordermod->add_order($order);

        return $this->json(['data' => $data]);
    }
    public function create()
    {
        $uid = UID();
        $ordermod = new Order;
        $payment = input('post.paymentType', '', 'serach_in');
        $cartid = input('post.cartid', '', 'serach_in');
        $msid = input('post.msid', '', 'serach_in');
        $tuanid = input('post.tuanid', '', 'serach_in');
        $jointuanid = input('post.jointuanid', '', 'serach_in');
        $deliverymode = input('post.deliverymode', '', 'serach_in');

        if (!empty($tuanid) && empty($jointuanid)) {
            $jointuanid = TuanFound::getTuanJoinTuanId($tuanid);
        }

        $technicalId = input('post.technicalId', '', 'serach_in');
        if ($technicalId == 'undefined') {
            $technicalId = '';
        }

        $tz_uuid = input('post.tz_uuid', '', 'serach_in');
        $tz_id = (int) input('post.tz_id', '', 'serach_in');
        if ($tz_uuid == 'undefined') {
            $tz_uuid = '';
        }

        $sid = input('post.sid/d');
        $address_id = input('post.address_id', '', 'serach_in');
        if ($address_id == 'undefined') {
            $address_id = '';
        }
        $remark = input('post.remark', '', 'serach_in');
        $shipping = input('post.shipping', '', 'serach_in');
        $couponId = input('post.couponId', '', 'serach_in');
        $goods_id = input('post.goodsId', '', 'serach_in');
        $otype = input('post.otype', '', 'serach_in');
        if ($otype == 'undefined') {
            $otype = 0;
        }
        $cat_id = input('post.cat_id', '', 'serach_in');
        $total = input('post.total', '', 'serach_in');
        $sku = input('post.sku');
        $is_PayPoints = input('post.is_PayPoints', '', 'serach_in');
        $goodsgiftcard_id = input('post.goodsgiftcardId', '', 'serach_in');
        $number = input('post.number', '', 'serach_in');
        $orderimage = input('post.orderimage', '', 'serach_in');
        $is_skumore = input('post.is_skumore', '', 'serach_in');
        $skumore = json_decode(input('post.skumore'), true);
        $combination_ids = input('post.combination_ids', '', 'serach_in');
        $servicetime = input('post.servicetime', '', 'serach_in');
        $is_times = input('post.is_times', '', 'serach_in');

        //加空判定
        if (!empty($servicetime)) {
            $servicetime =  explode(' ', $servicetime);
            if (!empty($servicetime[1])) {
                $timetmp = explode('-', $servicetime[1]);
            }
            $order['begin_time'] = strtotime($servicetime[0] . ' ' . $timetmp[0]);
            $order['end_time'] = strtotime($servicetime[0] . ' ' . $timetmp[1]);
        }
        $order['is_times'] = $is_times;

        $order['combination_ids'] = $combination_ids;
        $order['cartid'] = $cartid;
        $order['sid'] = $sid;
        if (empty($order['sid']) && !empty($tz_id)) {
            $order['sid'] = Store::getidbytzid($tz_id);
        }

        $order['couponId'] = $couponId;
        $order['goods_id'] = $goods_id;
        $order['deliverymode'] = $deliverymode;
        $order['otype'] = (int) $otype;
        $order['total'] = $total;
        $order['msid'] = $msid;
        $order['tuanid'] = $tuanid;
        $order['jointuanid'] = $jointuanid;
        $order['sku'] = $sku;
        $order['cat_id'] = $cat_id;
        $order['is_skumore'] = $is_skumore;
        $order['skumore'] = $skumore;
        $order['number'] = $number;
        $order['address_id'] = $address_id;
        $order['technicalId'] = $technicalId;
        $order['remark'] = $remark;
        $order['is_PayPoints'] = $is_PayPoints;
        $order['goodsgiftcard_id'] = $goodsgiftcard_id;
        $order['uid'] = $uid;

        //需要配送的
        if (!empty($shipping)) {
            $order['shipping_method'] =  '快递';
        } else {
            $order['shipping_method'] = '';
        }

        if (empty($order['address_id']) && empty($order['sid']) && empty($tz_uuid) && $deliverymode != 4) {
            throw new ValidateException('请先设置您的地址');
        }

        //支付方式
        if (!empty($payment)) {
            $order['payment_code'] = $payment;

            $paymentdata = Paymethod::where(['code' => $payment, 'weid' => weid()])->find();

            $order['pay_method_id'] = $paymentdata->id;
        }

        //var_dump($order);

        $data = $ordermod->add_order($order);

        if ($data['errno'] == 1) {
            throw new ValidateException($data['msg']);
        }

        //图片
        if ($data['id']) {
            OrderImage::where('order_id', $data['id'])->delete();
            if (!empty($orderimage)) {
                foreach (explode(',', $orderimage) as $image) {
                    OrderImage::create([
                        'order_id' => (int) $data['id'],
                        'weid' => weid(),
                        'image' => $image
                    ]);
                }
            }

            if (!empty($technicalId)) {
                $order_staff['order_id'] = $data['id'];
                $order_staff['uuid'] = $technicalId;
                $order_staff['uid'] = $uid;
                $order_staff['begin_time'] = $order['begin_time'];
                $order_staff['end_time'] = $order['end_time'];
                OrderStaff::addstaff($order_staff);
            }

            if (!empty($tz_uuid)) {
                $order_tuanzhang['order_id'] = $data['id'];
                $order_tuanzhang['uuid'] = $tz_uuid;
                $order_tuanzhang['uid'] = $uid;
                OrderTuanzhang::addtuanzhang($order_tuanzhang);
            }
        }

        return $this->json(['data' => $data]);
    }

    public function yuyuetime()
    {
        $id = input('post.id', '', 'intval');
        $timestype = input('post.timestype', '', 'serach_in');
        $yue_date = input('post.yue_date', '', 'serach_in');
        $servicetime = input('post.servicetime', '', 'serach_in');

        if ($timestype == 0) {
            $recently_day = get_week_recently_day($yue_date);
        } else {
            $recently_day = get_day_recently_day($yue_date);
        }

        if (!empty($servicetime)) {
            $timetmp = explode('-', $servicetime);
            $timescard['yue_begin_time'] = strtotime($recently_day . ' ' . $timetmp[0]);
            $timescard['yue_end_time'] = strtotime($recently_day . ' ' . $timetmp[1]);
        }

        $timescard['timestype'] = $timestype; //方式0周约1月约
        $timescard['yue_date'] = $yue_date;
        $timescard['order_id'] = $id;
        OrderTimescard::create($timescard);
        $orderinfo = Order::find($id);

        if (!empty($orderinfo)) {
            $orderinfo = $orderinfo->toArray();
            $timesmum = OrderCard::timesmum($orderinfo['id']);
            $timesused = OrderTimescardRecord::timesused($orderinfo['id']);
            $timesmum = $timesmum - $timesused;
            if ($timesmum > 0) {
                OrderTimescardRecord::where(['order_id' => $id, 'is_complete' => 0])->delete();
            }
        }
        return $this->json(['data' => $recently_day]);
    }

    public function close()
    {
        $this->cancel();
    }

    public function cancel()
    {
        $orderid = input('post.orderid', '', 'serach_in');

        Order::cancel_order($orderid);

        $data['orderid'] = $orderid;

        return $this->json(['data' => $data]);
    }

    public function receiving()
    {
        $orderid = input('post.orderid', '', 'serach_in');

        Order::receiving($orderid);

        $data['orderid'] = $orderid;

        return $this->json(['data' => $data]);
    }

    public function additional()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        $additional = input('post.additional', '', 'serach_in');

        $order['additional'] = $additional;
        Order::where('id', $orderid)->update($order);

        $order_info = Order::find($orderid);
        if (!empty($order_info)) {
            $order_info = $order_info->toArray();
        }
        MessageMini::sendMiniItional($order_info);
        return $this->json(['data' => $data]);
    }


    public function complete()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        try {
            $data =  Order::complete($orderid);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }

        return $this->json(['data' => $data]);
    }
    public function start()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        try {
            $data =  Order::start($orderid);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }

        return $this->json(['data' => $data]);
    }

    function staff()
    {
        $id = input('post.id', '', 'intval');
        $uuid = input('post.uuid', '', 'serach_in');
        $identity = input('post.identity', '', 'serach_in');


        if (empty($uuid)) {
            $uuid = UuidRelation::getuuid(UID(), $identity);
            $msg = '接单';
        } else {
            $msg = '派单';
            $is_distribution = 1;
        }

        $orderInfo = Order::find($id);

        if (!empty($orderInfo)) {
            $orderInfo = $orderInfo->toArray();

            if ($orderInfo['order_status_id'] == 2) {
                $order_history['order_status_id'] = 3;
                $orderup['order_status_id'] = 3;
            }

            try {
                $order_history['order_id'] = $orderInfo['id'];
                $order_history['remark'] = $msg;
                $order_history['notify'] = 1;

                OrderHistory::create($order_history);

                if ($uuid) {
                    if ($identity == "technical") {
                        $order_staff['order_id'] = $orderInfo['id'];
                        $order_staff['uuid'] = $uuid;
                        $order_staff['yue_begin_time'] = $orderInfo['begin_time'];
                        $order_staff['yue_end_time'] = $orderInfo['end_time'];
                        OrderStaff::addstaff($order_staff);
                        MessageMini::sendMiniStaff($orderInfo);
                        if (!empty($is_distribution)) {
                            MessageMp::distribution($uuid, $orderInfo);
                        }
                    } elseif ($identity == "store") {
                        $Store = Store::where(['uuid' => $uuid])->find();
                        if (!empty($Store)) {
                            $orderup['sid'] = $Store->id;
                        }
                    }
                }

                $orderup['id'] = $orderInfo['id'];
                order::update($orderup);
                $orderup['uid'] = $orderInfo['uid'];
                OrderCount::createuserdata($orderup);
            } catch (\Exception $e) {
                throw new ValidateException($e->getMessage());
            }
        }

        return $this->json(['msg' => $msg . '成功']);
    }

    //付款凭证上传
    public function offlinepayment()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        $img = input('post.image', '', 'serach_in');
        $remark = input('post.remark', '', 'serach_in');

        $order = new Order;
        $insert = $order->OfflinepaymentTap($orderid, $img, $remark);

        if ($insert) {
            Order::offline_order($orderid);

            $errno = 0;
            $message = '提交成功，请等待管理员审核！';

            return $this->json(['message' => $message, 'data' => $data]);
        } else {
            $errno = 1;
            $message = '提交失败，请重新尝试!';
            return $this->json(['message' => $message, 'data' => $data]);
        }
    }
    public function custom()
    {
        $id = input('post.id', '', 'intval');
        if (empty($id)) {
            $id = input('post.orderid', '', 'serach_in');
        }
        $registerfield['fields'] =  json_decode(input('post.registerfield'), true);

        $data = RegisterField::fieldToData($registerfield)['data'];

        Order::where('id', $id)->update(['customtext' => $data['customtext']]);

        $msg = '提交成功';
        return $this->json(['msg' => $msg, 'data' => $data]);
    }

    public function taketotal()
    {
        $address_id = input('post.address_id', '', 'serach_in');
        $take_address_id = input('post.take_address_id', '', 'serach_in');

        $data = Address::geterrands($address_id, $take_address_id);

        return $this->json(['data' => $data]);
    }

    public function total()
    {
        $charge = 0;
        $cartid = input('post.cartid', '', 'serach_in');
        $msid = input('post.msid', '', 'serach_in');
        $tuanid = input('post.tuanid', '', 'serach_in');
        $ptype = input('post.ptype', '', 'serach_in');
        $address_id = input('post.address_id', '', 'serach_in');
        $technicalId = input('post.technicalId', '', 'serach_in');
        if ($technicalId == 'undefined') {
            $technicalId = '';
        }

        if (empty($cartid)) {
            $goods_id = input('post.goodsId', '', 'serach_in');
            $sku = input('post.sku');
            $number = input('post.number', '', 'serach_in');
            $is_skumore = input('post.is_skumore', '', 'serach_in');
            $skumore = json_decode(input('post.skumore'), true);
            $Goods = new Goods;
            $buygoods = $Goods->cartGoods([
                'id' => $goods_id,
                'msid' => $msid,
                'tuanid' => $tuanid,
                'sku' => $sku,
                'quantity' => $number,
                'is_skumore' => $is_skumore,
                'skumore' => $skumore
            ]);
            $data["amountTotle"] = $buygoods['total'];
            $data["totalPayPoints"] = $buygoods['totalPayPoints'];
            $data['totalPointsPrice'] = $buygoods['totalPointsPrice'];
            $data['is_additional'] = $buygoods['is_additional'];

            if (empty($buygoods['weight'])) {
                $buygoods['weight'] = 0.1;
            }
            $weight = $buygoods['weight'];
            //var_dump($weight);
        } else {
            $cart = new Cart;
            $cartgoods = $cart->getlistbyid($cartid);
            $data["amountTotle"] = $cartgoods['totalprice'];
            $data["totalPayPoints"] = $cartgoods['totalPayPoints'];
            $data['totalPointsPrice'] = $cartgoods['totalPointsPrice'];
            $data['is_additional'] = $cartgoods['is_additional'];
            $weight = $cartgoods['weight'];
        }

        $data["thefare"] = 0;

        //收货人
        if (!empty($address_id)) {
            $shipping = Address::find($address_id);
            if (!empty($shipping)) {
                $shipping = $shipping->toArray();
            }
            if ($ptype == 1) {

                $shipping_city_id = Area::get_area_id($shipping['city_name']);

                $Transport = (new TransportExtend)->calc_transport($weight, $shipping_city_id);
                $amountLogistics = $Transport['price'];
            }

            if ($technicalId && $ptype == 2) {
                $thefare = Address::getthefare($address_id, $technicalId);
                $data["distance"] = $thefare['distance'];
                $data["thefare"] = $thefare['thefare'];
            }
        }

        $Membermob = new Member;
        $Member = $Membermob->getUserByWechat();

        //订阅消息模板
        $tmplIds = Config::getconfig('subscribemessage');
        $data['tmplIds'][] = $tmplIds['pay_tpl'];
        $data['tmplIds'][] = $tmplIds['staff_tpl'];
        if ($data['is_additional'] == 1) {
            $data['tmplIds'][] = $tmplIds['itional_tpl'];
        } else {
            $data['tmplIds'][] = $tmplIds['complete_tpl'];
        }

        $data["memberPoints"] = $Member['points'];
        $test['shipping_method'] = '快递';
        $test['weight'] = $weight;
        $test['shipping_city_id'] = $shipping_city_id;
        $data["amountLogistics"] = (float) $amountLogistics;
        $data['charge'] = $charge;
        $data["isNeedLogistics"] = 1;
        $data['test'] = $test;
        $data["amountTotle"] = round($data["amountTotle"], 2);


        return $this->json(['data' => $data]);
    }

    //直接使用余额支付
    public function pay()
    {

        $orderid = input('post.orderid', '', 'serach_in');

        //付款前检查库存
        $result = Order::getGoodsquantitiy($orderid);

        if ($result) {
            $orderinfo = Order::find($orderid);

            if (!empty($orderinfo)) {
                $orderinfo = $orderinfo->toArray();
                $money = $orderinfo['total'];
                $Member = Member::find(UID());
                if (!empty($Member) && ($Member->balance >= $money)) {
                    Member::where('id', UID())
                        ->dec('balance', $money)
                        ->update();

                    Order::update(['id' => $orderid, 'pay_time' => time(), 'payment_code' => 'balance_pay']);

                    $ordermod = new Order;
                    $ordermod->pay_order(['order_num_alias' => $orderinfo['order_num_alias'], 'nosendmessage' => 1]);
                } else {
                    $errno = 1;
                    $message = '余额不足';
                }
            } else {
                $errno = 1;
                $message = '订单不存在';
            }
        } else {
            $errno = 2;
            $message = '库存量不足';
        }

        return $this->json(['msg' => $message, 'message' => $message, 'errno' => $errno, 'data' => $data]);
    }

    //使用购物卡支付
    public function goodsgiftcardpay()
    {

        $orderid = input('post.orderid', '', 'serach_in');

        //付款前检查库存
        $result = Order::getGoodsquantitiy($orderid);

        if ($result) {
            $orderinfo = Order::find($orderid);

            if (!empty($orderinfo)) {
                $orderinfo = $orderinfo->toArray();
                $money = $orderinfo['total'];
                $OrderCard = OrderCard::find($orderinfo['goodsgiftcard_id']);

                if (!empty($OrderCard) && ($OrderCard->balance >= $money)) {
                    OrderCard::where('id', $orderinfo['goodsgiftcard_id'])
                        ->dec('balance', $money)
                        ->update();

                    Order::update(['id' => $orderid, 'pay_time' => time(), 'payment_code' => 'goodsgiftcard_pay']);
                    $ordermod = new Order;
                    $ordermod->pay_order(['order_num_alias' => $orderinfo['order_num_alias'], 'nosendmessage' => 1]);
                } else {
                    $errno = 1;
                    $message = '余额不足';
                }
            } else {
                $errno = 1;
                $message = '订单不存在';
            }
        } else {
            $errno = 2;
            $message = '库存量不足';
        }

        return $this->json(['msg' => $message, 'message' => $message, 'errno' => $errno, 'data' => $data]);
    }

    public function delivery()
    {

        $orderid = input('post.orderid', '', 'serach_in');
        $orderinfo = Order::find($orderid);
        if (!empty($orderinfo)) {
            Order::settlement($orderid);
        } else {
            $errno = 1;
            $message = '订单不存在';
        }
        return $this->json(['message' => $message, 'errno' => $errno, 'data' => $data]);
    }

    //2023-3-9已移到PaymethodController的list
    //支付方式列表
    public function paymethod()
    {

        $data = Paymethod::where(['weid' => weid(), 'status' => 1])
            ->order('sort asc')
            ->select()
            ->toArray();
        $data[0]['default'] = $data[0]['default']['code'];
        foreach ($data as $i => $vo) {
            if (empty($vo['code'])) {
                unset($data[$i]);
            }
            if (($vo['code'] == 'wx_pay')) {
                $data[0]['default'] = $vo['code'];
            }
        }
        if (empty($data)) {
            Paymethod::update(['code' => 'wx_pay', 'weid' => 0]);
            $data  = Paymethod::where(['weid' => 0])->order('sort asc')->select()->toArray();
        }
        if (empty($data[0]['default'])) {
            $data[0]['default'] = $data[0]['code'];
        }
        return $this->json(['data' => $data]);
    }
}
