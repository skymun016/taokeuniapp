<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Order;
use app\model\OrderStaff;
use app\model\Store;
use app\model\OrderStatus;
use app\model\Paymethod;
use app\model\Member;
use app\model\OrderTimescard;
use app\model\Category;
use app\model\RefundAddress;
use app\model\OrderRefund;
use app\model\Operatingcity;
use app\model\Tuanzhang;

class OrderrefundController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = Order::where(['weid' => $weid, 'order_status_id' => 6]);

		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}
		if (!empty($this->ocid)) {

			$Operatingcitydata = Operatingcity::find($this->ocid);
			if ($Operatingcitydata) {
				$Operatingcitydata = $Operatingcitydata->toArray();
				if(empty($Operatingcitydata['areatype'])){
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
		}
		if (!empty($this->tzid)) {

			$Tuanzhangdata = Tuanzhang::find($this->tzid);

			if ($Tuanzhangdata) {
				$Tuanzhangdata = $Tuanzhangdata->toArray();
				$query->where('shipping_district_name', $Tuanzhangdata['district_name']);
			} else {
				$query->where('shipping_city_name', '无');
			}
		}

		$query->with(['orderRefund','orderTimescard', 'member', 'orderGoods', 'paymethod']);

		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {

			$vo['sid'] = Store::getTitle($vo['sid']);
			$vo['staff'] = OrderStaff::staff($vo['id']);
			$vo['pay_time'] = time_format($vo['pay_time']);

			if (empty($vo['orderGoods'])) {
				if (!empty($vo['cat_id'])) {
					$vo['orderGoods'][0]['image'] = Category::getImage($vo['cat_id']);
				}
				$vo['orderGoods'][0]['name'] = '【用户发布需求】' . $vo['pay_subject'];
			}
			if ($vo['ptype'] == 1) {
				$vo['order_status'] = OrderStatus::get_order_status_name($vo['order_status_id']);
				$vo['order_status'] = $vo['order_status'] . '【' . refundStatus(($vo['orderRefund']['refund_status'])) . '】';
				$vo['orderRefund']['refund_type_name'] = refundType($vo['orderRefund']['refund_type']);
				$vo['shipping_type'] = getgoodsdeliverymode($vo['deliverymode']);
			} else {
				$vo['order_status'] = OrderStatus::get_order_status_name($vo['order_status_id'], 'service');
				$vo['order_status'] = $vo['order_status'] . '【' . refundStatus_yuyue(($vo['orderRefund']['refund_status'])) . '】';
				$vo['orderRefund']['refund_type_name'] = refundType_yuyue($vo['orderRefund']['refund_type']);
				$vo['shipping_type'] = getservicedeliverymode($vo['deliverymode']);
			}

			$vo['payment_code_name'] = paymentCode($vo['payment_code']);

			if ($vo['is_times'] == 1) {
				$OrderTimescard = OrderTimescard::where('order_id', $vo['id'])->order('id asc')->select()->toArray();
                if ($OrderTimescard) {
                    foreach ($OrderTimescard as $tcvo) {
                        if ($tcvo['yue_date']) {
                            if ($tcvo['orderTimescard']['timestype'] == 1) {
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
			} else {
				$vo['yue_time'] = time_format($vo['begin_time']) . ' 到 ' . date('H:i', $vo['end_time']);
			}

			if (!empty($vo['orderGoods'])) {
				foreach ($vo['orderGoods'] as &$vvo) {
					$vvo['image'] = toimg($vvo['image']);
				}
			}
		}

		$data['data'] = $res;
		$RefundAddress = RefundAddress::where(['weid' => weid(), 'status' => 1])->order('id asc')->select()->toArray();
		$data['field_data']['RefundAddress'] = $RefundAddress;

		return $this->json($data);
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');

		$data = Order::order_info($id);

		if ($data['orderInfo']['sid'] == 0) {
			$data['orderInfo']['sid'] = '';
		}

		return $this->json(['data' => $data]);
	}

	// 处理售后订单
	public function refundhandle()
	{
		$postdata = input('post.');
		try {
			if ($postdata['OrderRefund']['refund_status'] == 1) {
				$res = Order::refund_order($postdata['OrderRefund']['order_id'], $postdata['OrderRefund']['response_time'], $postdata['OrderRefund']);
				if ($res['code']) {
					$postdata['OrderRefund']['response_time'] = time();
					$res['code'] = OrderRefund::where('id', $postdata['OrderRefund']['id'])->update($postdata['OrderRefund']);
				}
				if ($res['code']) {
					return $this->json(['msg' => '微信支付退款处理成功！']);
				} else {
					throw new ValidateException($res['return_msg'] . $res['err_code_des']);
				}
			} else {
				unset($postdata['OrderRefund']['refund_price']);
				OrderRefund::where('id', $postdata['OrderRefund']['id'])->update($postdata['OrderRefund']);
				if ($postdata['OrderRefund']['refund_type'] == 1) {
					Order::update(['order_status_id' => 2, 'id' => $postdata['OrderRefund']['order_id']]);
				}

				if ($postdata['OrderRefund']['refund_type'] == 2) {
					Order::update(['order_status_id' => 5, 'id' => $postdata['OrderRefund']['order_id']]);
				}

				return $this->json(['msg' => '处理成功！']);
			}
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
	}

	function delete()
	{
		$idx =  $this->request->post('id', '', 'serach_in');
		if (!$idx) throw new ValidateException('参数错误');
		if (!is_array($idx)) {
			$idx = explode(',', $idx);
		}

		Order::destroy(['id' => $idx], true);
		OrderRefund::where(['order_id' => $idx])->delete();
		return $this->json(['msg' => '操作成功']);
	}
}
