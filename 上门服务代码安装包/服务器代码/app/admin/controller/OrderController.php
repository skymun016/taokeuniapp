<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Order;
use app\model\OrderStaff;
use app\model\OrderCount;
use app\model\Store;
use app\model\Express;
use app\model\OrderHistory;
use app\model\OrderExpress;
use app\model\Technical;
use app\model\MessageMp;
use app\model\MessageMini;
use app\model\Paymethod;
use app\model\RegisterField;
use app\model\Operatingcity;
use app\model\OrderTuanzhang;
use app\model\Goods;
use app\model\Cashregister;
use app\model\Config;
use app\samos\wechat\MiniProgram;

class OrderController extends Base
{

	function index()
	{
		$status = input('post.status', '', 'serach_in');
		$query = $this->setSearch();

		$querycount = clone $query;
		$countdata['statuscount1'] = Order::statuscount(clone $querycount, 1);
		$countdata['statuscount2'] = Order::statuscount(clone $querycount, 2);
		$countdata['statuscount3'] = Order::statuscount(clone $querycount, 3);
		$countdata['statuscount4'] = Order::statuscount(clone $querycount, 4);
		$countdata['statuscount5'] = Order::statuscount(clone $querycount, 5);
		$countdata['statuscount6'] = Order::statuscount(clone $querycount, 6);
		$countdata['statuscount7'] = Order::statuscount(clone $querycount, 7);

		if (!empty($status)) {
			$query->where('order_status_id', $status);
		}


		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();
		$sql = $query->getLastsql();
		foreach ($res['data'] as &$vo) {
			$vo = Order::conversion($vo);
			Order::where('id', $vo['id'])->update(['searchkeyword' =>  '']);
		}

		$data['data'] = $res;
		$data['sql'] = $sql;

		$data['countdata'] = $countdata;
		return $this->json($data);
	}

	function setSearch()
	{
		$weid = weid();
		$path = input('post.path', '', 'serach_in');
		$keyword = input('post.keyword', '', 'serach_in');
		$deliverymode = input('post.deliverymode', '', 'serach_in');

		$uid = input('post.uid', '', 'intval');
		$create_time = input('post.create_time', '', 'serach_in');
		$query = Order::where(['weid' => $weid]);

		if (!empty($deliverymode)) {
			$query->where('deliverymode', $deliverymode);
		}

		if (!empty($uid)) {
			$query->where('uid', $uid);
		}
		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}
		if (!empty($this->ocid)) {

			$Operatingcitydata = Operatingcity::find($this->ocid);

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
					$query->where('shipping_province_name', 'like', '%' . $Operatingcitydata['province_name'] . '%');
				}
			} else {
				$query->where('shipping_city_name', '无');
			}
		}

		if (!empty($this->tzid)) {

			if (!empty($this->tzid)) {
				$query->where('sid', Store::getidbytzid($this->tzid));
			}
		}

		if ($path == '/order/service' || $path == '/order/storeservice') {
			$query->where('ptype', 2)->where('is_times', 0);
		} elseif ($path == '/order/timescard') {
			$query->where('ptype', 2)->where('is_times', 1);
		} elseif ($path == '/order/goodsgiftcard') {
			$query->where('ptype', 2)->where('is_times', 3);
		} elseif ($path == '/order/goods') {
			$query->where('ptype', 1)->where('is_times', 0);
		}

		$query->with(['member', 'orderGoods', 'paymethod']);

		if (!empty($create_time)) {
			$query->where('create_time', 'between', [strtotime($create_time[0]), strtotime($create_time[1])]);
		}
		if (!empty($keyword)) {
			$query->where('name|searchkeyword|pay_subject|shipping_name|shipping_tel|shipping_province_name|shipping_city_name|shipping_district_name|shipping_address|order_num_alias', 'like', '%' . $keyword . '%');
		}
		return $query;
	}

	public function total()
	{

		$postdata = input('post.');
		$skumore = json_decode($postdata['skumore'], true);

		$Goods = new Goods;
		$buygoods = $Goods->cartGoods([
			'id' => $postdata['goods_id'],
			'sku' => $postdata['sku'],
			'quantity' => $postdata['number'],
			'is_skumore' => $postdata['is_skumore'],
			'skumore' => $skumore
		]);
		$data["amountTotle"] = $buygoods['total'];
		$data["amountTotle"] = round($data["amountTotle"], 2);

		return $this->json(['data' => $data]);
	}

	function payorderquery()
	{
		$postdata = input('post.');
		$order_id = $postdata['order_id'];
		$orderinfo = Order::find($order_id);

		if ($orderinfo) {
			$orderinfo  = $orderinfo->toArray();
		}
		$payment = \app\samos\wechat\WxPaymethod::makepay();

		$message = $payment->order->queryByOutTradeNumber($orderinfo['order_num_alias']);

		$data['orderinfo'] = $orderinfo;
		$data['message'] = $message;

		if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态

			if ($message['result_code'] === 'SUCCESS') {
				$pay_time = time();
				Order::where(['id' => $orderinfo['id']])->update(['pay_time' => $pay_time]);
				$ordermod = new Order;
				$ordermod->pay_order(['order_num_alias' => $orderinfo['order_num_alias']]);
			}
		}

		return $this->json(['data' => $data]);
	}

	function cashregister()
	{
		$msg = '收银成功';
		$postdata = input('post.');
		$ordermod = new Order;
		$payment = 'wx_pay';
		$order['operator_id'] = $this->userInfo['id'];
		$cashinfo = Cashregister::getcash(['operator_id' => $order['operator_id']]);

		if ($cashinfo['uid']) {
			$order['uid'] = $cashinfo['uid'];
		}

		$order['is_cashregister'] = 1;
		$order['auth_code'] = $postdata['auth_code'];

		if (!empty($this->sid)) {
			$order['sid'] = $this->sid;
		}

		if (!empty($this->tzid)) {
			$order['sid'] = (int) Store::getidbytzid($this->tzid);
		}

		//支付方式
		if (!empty($payment)) {
			$order['payment_code'] = $payment;

			$paymentdata = Paymethod::where(['code' => $payment, 'weid' => weid()])->find();

			$order['pay_method_id'] = $paymentdata->id;
		}

		$area = $postdata['area'];
		$order['shipping_province_name'] = $area[0];
		$order['shipping_city_name'] = $area[1];
		$order['shipping_district_name'] = $area[2];

		$order['shipping_name'] = $postdata['shipping_name'];
		$order['shipping_tel'] = $postdata['shipping_tel'];
		$order['shipping_address'] = $postdata['shipping_address'];

		$order['remark'] = $postdata['remark'];

		//var_dump($order);

		$orderinfo = $ordermod->add_order($order);

		if (!empty($orderinfo['id'])) {

			$payment = \app\samos\wechat\WxPaymethod::makepay();

			$money = round($orderinfo['total'], 2);
			$money = floatval($money * 100);

			$message = $payment->pay([
				'body' => $orderinfo['pay_subject'],
				'out_trade_no' => $orderinfo['order_num_alias'],
				'total_fee' => $money,
				'auth_code' => $orderinfo['auth_code'],
			]);

			if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态

				if ($message['result_code'] === 'SUCCESS') {
					$pay_time = time();
					Order::where(['id' => $orderinfo['id']])->update(['pay_time' => $pay_time]);
					$ordermod = new Order;
					$ordermod->pay_order(['order_num_alias' => $orderinfo['order_num_alias']]);
				}
			}
		}

		$data['orderinfo'] = $orderinfo;
		$data['message'] = $message;

		return $this->json(['msg' => $msg, 'data' => $data]);
	}

	function add()
	{
		$msg = '添加成功';
		$postdata = input('post.');
		$Configdata = Config::getconfig();
		$ordermod = new Order;

		if (!empty($postdata['order_num_alias'])) {
			if (Order::where(['order_num_alias' => $postdata['order_num_alias']])->find()) {
				throw new ValidateException('订单编号已存在');
			} else {
				$order['order_num_alias'] = $postdata['order_num_alias'];
			}
		}
		if ($Configdata['external_order_pay'] == 1) {
			$payment = 'offline_pay';
		} else {
			$payment = 'wx_pay';
		}

		$servicetime = input('post.servicetime', '', 'serach_in');
		if (!empty($servicetime)) {
			$servicetime =  explode(' ', $servicetime);
		}
		if (empty($servicetime[1])) {
			$timetmp = explode('-', $servicetime[1]);
		}

		$order['begin_time'] = strtotime($servicetime[0] . ' ' . $timetmp[0]);
		$order['end_time'] = strtotime($servicetime[0] . ' ' . $timetmp[1]);
		$order['goods_id'] = $postdata['goods_id'];
		$order['total'] = $postdata['total'];
		$order['number'] = $postdata['number'];

		//支付方式
		if (!empty($payment)) {
			$order['payment_code'] = $payment;

			$paymentdata = Paymethod::where(['code' => $payment, 'weid' => weid()])->find();

			$order['pay_method_id'] = $paymentdata->id;
		}

		$area = $postdata['area'];
		$order['shipping_province_name'] = $area[0];
		$order['shipping_city_name'] = $area[1];
		$order['shipping_district_name'] = $area[2];

		$order['shipping_name'] = $postdata['shipping_name'];
		$order['shipping_tel'] = $postdata['shipping_tel'];
		$order['shipping_address'] = $postdata['shipping_address'];

		$order['remark'] = $postdata['remark'];

		//var_dump($order);

		$orderinfo = $ordermod->add_order($order);

		if (!empty($orderinfo['id'])) {

			if ($Configdata['external_order_pay'] == 1) {

				$pay_time = time();
				Order::where(['id' => $orderinfo['id']])->update(['pay_time' => $pay_time]);
				$ordermod = new Order;
				$ordermod->pay_order(['order_num_alias' => $orderinfo['order_num_alias']]);
			}
		}

		return $this->json(['msg' => $msg, 'data' => $orderinfo]);
	}

	function update()
	{
		$msg = '修改成功';
		$postdata = input('post.');
		$orderInfo = $postdata['orderInfo'];

		try {
			$order_history['order_status_id'] = $orderInfo['order_status_id'];
			$order_history['order_id'] = $orderInfo['id'];
			$order_history['remark'] = $postdata['words'];
			if (empty($order_history['remark'])) {
				$order_history['remark'] = '修改订单';
			}
			$order_history['notify'] = 1;
			OrderHistory::create($order_history);
			$orderup = $orderInfo;
			unset($orderup['create_time']);
			unset($orderup['update_time']);
			unset($orderup['pay_time']);
			unset($orderup['ServiceTime']);
			unset($orderup['ServiceTime']);
			if (!empty($postdata['is_express'])) {
				$orderup['is_express'] = $postdata['is_express'];
			}
			$fieldsdata = RegisterField::fieldToData($postdata, 'pc')['data'];
			$orderup['customtext'] = $fieldsdata['customtext'];
			Order::update($orderup);
			$orderup['uid'] = $orderInfo['uid'];
			OrderCount::createuserdata($orderup);
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
		return $this->json(['msg' => $msg]);
	}

	public function delivery()
	{

		$msg = '操作成功';
		$orderid = input('post.id');
		$orderinfo = Order::find($orderid);
		if (!empty($orderinfo)) {
			Order::settlement($orderid);
		} else {
			$msg = '订单不存在';
		}
		return $this->json(['msg' => $msg]);
	}

	function staff()
	{
		$postdata = input('post.');
		$orderInfo = $postdata['orderInfo'];

		try {

			if (empty($this->sid)) {

				if (empty($orderInfo['sendto'])) {
					throw new ValidateException('请选择指派给');
				}
				//平台派单
				if ($orderInfo['sendto'] == 2) {
					if (empty($postdata['uuid'])) {
						throw new ValidateException('请选择师傅');
					}

					$order_staff['order_id'] = $orderInfo['id'];
					$order_staff['uuid'] = $postdata['uuid'];
					$order_staff['begin_time'] = $orderInfo['begin_time'];
					$order_staff['end_time'] = $orderInfo['end_time'];

					OrderStaff::addstaff($order_staff);
					MessageMini::sendMiniStaff($orderInfo);
					MessageMp::distribution($postdata['uuid'], $orderInfo);
					$orderup['sid'] = 0;
					$msg = '派单成功';
				} elseif ($orderInfo['sendto'] == 1) {
					if (empty($orderInfo['sid'])) {
						throw new ValidateException('请选择商家');
					}
					OrderStaff::where('order_id', $orderInfo['id'])->delete();
					$msg = '派单成功';
					$orderup['sid'] = $orderInfo['sid'];
				}
			} else {
				//商家派单
				$order_staff['order_id'] = $orderInfo['id'];
				$order_staff['uuid'] = $postdata['siduuid'];
				$order_staff['begin_time'] = $orderInfo['begin_time'];
				$order_staff['end_time'] = $orderInfo['end_time'];

				OrderStaff::addstaff($order_staff);
				MessageMini::sendMiniStaff($orderInfo);
				MessageMp::distribution($postdata['uuid'], $orderInfo);
				$msg = '派单成功';
			}

			if ($orderInfo['order_status_id'] == 2) {
				$order_history['order_status_id'] = 3;
				$orderup['order_status_id'] = 3;
			} else if ($orderInfo['order_status_id'] == 8) {
				$order_history['order_status_id'] = 9;
				$orderup['order_status_id'] = 9;
			}

			$order_history['order_id'] = $orderInfo['id'];
			$order_history['remark'] = '派单';
			$order_history['notify'] = 1;
			OrderHistory::create($order_history);
			$orderup['id'] = $orderInfo['id'];
			$orderup['sendto'] = $orderInfo['sendto'];

			Order::update($orderup);
			$orderup['uid'] = $orderInfo['uid'];
			OrderCount::createuserdata($orderup);
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
		return $this->json(['msg' => $msg]);
	}
	function send()
	{
		$postdata = input('post.');
		$orderInfo = $postdata['orderInfo'];

		if ($orderInfo['order_status_id'] == 2) {
			$order_history['order_status_id'] = 3;
			$orderup['order_status_id'] = 3;
		} else if ($orderInfo['order_status_id'] == 8) {
			$order_history['order_status_id'] = 9;
			$orderup['order_status_id'] = 9;
		}

		try {

			$order_history['order_id'] = $orderInfo['id'];
			$order_history['remark'] = $postdata['words'];
			$order_history['notify'] = 1;
			OrderHistory::create($order_history);
			$order_express['weid'] = weid();
			$order_express['order_id'] = $orderInfo['id'];
			$order_express['expressname'] = Express::getExname($postdata['express_code']);
			$order_express['express_code'] = $postdata['express_code'];
			$order_express['express_no'] = $postdata['express_no'];
			if (OrderExpress::where('order_id', $orderInfo['id'])->find()) {
				OrderExpress::where('order_id', $orderInfo['id'])->update($order_express);
				$msg = '修改物流信息成功';
			} else {
				OrderExpress::create($order_express);
				$msg = '发货成功';
			}

			$orderup['is_express'] = $postdata['is_express'];

			$orderup['id'] = $orderInfo['id'];
			Order::update($orderup);
			$orderup['uid'] = $orderInfo['uid'];
			OrderCount::createuserdata($orderup);
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
		return $this->json(['msg' => $msg]);
	}

	function getSendInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$orderInfo = Order::find($id);
		if ($orderInfo) {
			$orderInfo = $orderInfo->toArray();

			if ($orderInfo['deliverymode'] == 5) {
				$tuanzhang = OrderTuanzhang::getTuanzhang($orderInfo['id']);
				if (!empty($tuanzhang)) {
					$orderInfo['shipping_name'] = $tuanzhang['title'];
					$orderInfo['shipping_tel'] = $tuanzhang['tel'];
					$orderInfo['shipping_address'] = $tuanzhang['region_name'];
				}
			}
		}

		$data = OrderExpress::where('order_id', $id)->find();
		if ($data) {
			$data = $data->toArray();
		}

		$data['orderInfo'] = $orderInfo;
		$data['is_express'] = $orderInfo['is_express'];

		return $this->json(['data' => $data]);
	}

	function offlinepay()
	{

		$postdata = input('post.');
		$orderInfo = $postdata['orderInfo'];

		$id = $orderInfo['id'];

		$orderinfo = Order::where(['id' => $id])->find();

		if (!empty($orderinfo)) {

			$pay_time = time();
			Order::where(['id' => $id])->update(['pay_time' => $pay_time]);

			$ordermod = new Order;
			$ordermod->pay_order(['order_num_alias' => $orderinfo['order_num_alias']]);
		}
		return $this->json(['msg' => '操作成功']);
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');

		$data = Order::order_info($id);
		$data['sid'] = (int) $this->sid;
		$data['sidarray'] = Store::getpcarray();

		$data['technical'] = Technical::getpcarraydetailed($data['sid'], $data['orderInfo']['cate_ids'], $data['orderInfo']['shipping_city_name']);


		if ($data['orderInfo']['deliverymode'] == 5) {
			$tuanzhang = OrderTuanzhang::getTuanzhang($data['orderInfo']['id']);
			if (!empty($tuanzhang)) {
				$data['orderInfo']['shipping_name'] = $tuanzhang['title'];
				$data['orderInfo']['shipping_tel'] = $tuanzhang['tel'];
				$data['orderInfo']['shipping_address'] = $tuanzhang['region_name'];
			}
		}

		if ($data['orderInfo']['payment_code'] == 'offline_pay') {
			$data['offline_img'] = Order::getoffline($id);
		}

		if ($data['orderInfo']['sid'] == 0) {
			$data['orderInfo']['sid'] = '';
		}
		if (empty($data['orderInfo']['member'])) {
			$data['orderInfo']['member'] = [];
		}
		$data['is_express']	= 1;

		foreach ($data['goods'] as $gvo) {
			$data['goodsquantity'] = $data['goodsquantity'] + $gvo['quantity'];
		}

		$data['uuid'] = OrderStaff::getuuid($data['orderInfo']['id']);

		if ($data['sid']) {
			$data['siduuid'] = $data['uuid'];
		}

		$page = 'pagesA/my/myOrder/yuyueDetail?id=' . $data['orderInfo']['id'];

		$data['orderInfo']['qrcode'] =  MiniProgram::getQrcode2($page);

		$page = 'pagesA/my/publicOrder/orderpay';
		$scene = $data['orderInfo']['id'];
		$data['orderInfo']['payqrcode'] =  MiniProgram::getQrcode($scene, $page);

		$customtext = iunserializer($data['orderInfo']['customtext']);

		$RegisterField = RegisterField::getinputField('complete');
		foreach ($RegisterField as &$vo) {

			if ($vo['is_sys'] == 1) {
			} else {
				$vo['fieldsvalue'] = $customtext[$vo['inputtype']][$vo['id']];
			}
			if ($vo['inputtype'] == 'pics') {
				$vo['fieldsvalue1'] = $vo['fieldsvalue'];
				if (empty($vo['fieldsvalue'])) {
					$vo['fieldsvalue'] = [];
				} else {
					$vo['fieldsvalue'] = setPicsView($vo['fieldsvalue']);
				}
			}
		}
		$data['fields'] = $RegisterField;
		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new Order());
	}
	function getTechnical()
	{
		$sid =  $this->request->post('sid', '', 'serach_in');
		$data['technical'] = Technical::getpcarray($this->sid);

		return $this->json(['data' => $data]);
	}
	function getExpress()
	{
		$data['expressArray'] = Express::getpcarray();
		return $this->json(['data' => $data]);
	}
	function dumpdata()
	{
		$datalist = [];
		$page = $this->request->post('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;
		$query = $this->setSearch();

		$status = input('post.status', '', 'serach_in');

		if (!empty($status)) {
			$query->where('order_status_id', $status);
		}

		$count = $query->count();

		$res = $query->order('id desc')->limit(($page - 1) * $limit, $limit)->select()->toArray();
		//var_dump($query->getLastSql());

		$servicedumpField = [
			[
				'fieldsmingcheng' => 'order_num_alias',
				'viewmingcheng' => '订单号'
			],
			[
				'fieldsmingcheng' => 'pay_subject',
				'viewmingcheng' => '预约服务'
			],
			[
				'fieldsmingcheng' => 'total',
				'viewmingcheng' => '订单金额'
			],
			[
				'fieldsmingcheng' => 'sid',
				'viewmingcheng' => '派单门店'
			],
			[
				'fieldsmingcheng' => 'staff',
				'viewmingcheng' => '派单师傅'
			],
			[
				'fieldsmingcheng' => 'member_nickname',
				'viewmingcheng' => '下单用户'
			],
			[
				'fieldsmingcheng' => 'shipping_name',
				'viewmingcheng' => '联系人'
			],
			[
				'fieldsmingcheng' => 'shipping_tel',
				'viewmingcheng' => '电  话'
			],
			[
				'fieldsmingcheng' => 'yue_time',
				'viewmingcheng' => '预约时间'
			], [
				'fieldsmingcheng' => 'shipping_city_name',
				'viewmingcheng' => '所在城市'
			],
			[
				'fieldsmingcheng' => 'shipping_address',
				'viewmingcheng' => '详细地址'
			],
			[
				'fieldsmingcheng' => 'order_status',
				'viewmingcheng' => '订单状态'
			],
			[
				'fieldsmingcheng' => 'payment_code_name',
				'viewmingcheng' => '支付方式'
			],
			[
				'fieldsmingcheng' => 'pay_time',
				'viewmingcheng' => '支付时间'
			]
		];

		$goodsdumpField = [
			[
				'fieldsmingcheng' => 'order_num_alias',
				'viewmingcheng' => '订单号'
			],
			[
				'fieldsmingcheng' => 'pay_subject',
				'viewmingcheng' => '商品'
			],
			[
				'fieldsmingcheng' => 'total',
				'viewmingcheng' => '订单金额'
			],
			[
				'fieldsmingcheng' => 'member_nickname',
				'viewmingcheng' => '下单用户'
			],
			[
				'fieldsmingcheng' => 'order_status',
				'viewmingcheng' => '订单状态'
			],
			[
				'fieldsmingcheng' => 'payment_code_name',
				'viewmingcheng' => '支付方式'
			],
			[
				'fieldsmingcheng' => 'pay_time',
				'viewmingcheng' => '支付时间'
			],
			[
				'fieldsmingcheng' => 'shipping_name',
				'viewmingcheng' => '收货人'
			],
			[
				'fieldsmingcheng' => 'shipping_tel',
				'viewmingcheng' => '电话'
			],
			[
				'fieldsmingcheng' => 'shipping_city_name',
				'viewmingcheng' => '城市'
			],
			[
				'fieldsmingcheng' => 'shipping_address',
				'viewmingcheng' => '地址'
			],
			[
				'fieldsmingcheng' => 'shipping_time',
				'viewmingcheng' => '发货时间'
			],
			[
				'fieldsmingcheng' => 'shipping_express_id',
				'viewmingcheng' => '快递公司'
			],
			[
				'fieldsmingcheng' => 'shipping_code',
				'viewmingcheng' => '快递单号'
			]
		];

		if ($res[0]['ptype'] == 1) {
			$dumpField = $goodsdumpField;
		} elseif ($res[0]['ptype'] == 2) {
			$dumpField = $servicedumpField;
		}

		foreach ($res as $k => $vo) {
			$vo = Order::conversion($vo);

			foreach ($dumpField as $key => $fvo) {
				$datalist[$k][$key] = $vo[$fvo['fieldsmingcheng']];
			}
		}
		foreach ($dumpField as $key => $vo) {
			$data['header'][$key] = $vo['viewmingcheng'];
		}

		$data['percentage'] = ceil($page * 100 / ceil($count / $limit));
		$data['filename'] = '订单.' . config('my.dump_extension');
		$data['data'] = $datalist;
		return $this->json($data);
	}
}
