<?php

namespace app\model;

use think\Model;
use app\samos\wechat\MiniProgram;

class MessageMp extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'message';

	public static function itional_pay($orderInfo)
	{
		$orderInfo['pay_time'] = time_format($orderInfo['pay_time']);
		$messagetpl = Config::getconfig('messagetpl');
		$param['template_id'] = $messagetpl['pay_tpl'];

		$param['uuid'] = OrderStaff::getuuid($orderInfo['id']);

		$param['uid'] = UuidRelation::getuid($param['uuid']);
		$param['title'] = '客户已支付尾款通知';
		$param['pages'] = '/pagesA/my/admintechnical/orderDetail';
		$param['query'] = 'id=' . $orderInfo['id'];
		$param['content'] = '客户已支付尾款通知';

		$param['data']['character_string1']['value'] = $orderInfo['order_num_alias'];
		$param['data']['thing3']['value'] = mb_substr($orderInfo['pay_subject'], 0, 20);
		$param['data']['time5']['value'] = $orderInfo['pay_time'];
		$param['data']['amount4']['value'] = $orderInfo['total'];

		if (self::send($param)) {
			self::sendmptplmessage($param);
		};
	}
	//派单
	public static function distribution($uuid, $orderInfo)
	{
		$orderInfo['pay_time'] = time_format($orderInfo['pay_time']);
		$messagetpl = Config::getconfig('messagetpl');
		$param['template_id'] = $messagetpl['technical_tpl'];

		$param['uuid'] = $uuid;
		$param['uid'] = UuidRelation::getuid($param['uuid']);
		$param['title'] = '平台派单通知';

		$param['content'] = '您有一个新的平台订单，请及时处理';
		$param['data']['character_string2']['value'] = $orderInfo['order_num_alias'];
		$param['data']['thing8']['value'] = mb_substr($orderInfo['pay_subject'], 0, 18);
		$param['data']['amount10']['value'] = $orderInfo['total'];
		$param['data']['thing16']['value'] = $orderInfo['shipping_name'];
		$param['data']['phone_number7']['value'] = encryptTel($orderInfo['shipping_tel']);

		$param['pages'] = '/pagesA/my/admintechnical/orderDetail';
		$param['query'] = 'id=' . $orderInfo['id'];

		if (self::send($param)) {
			self::sendmptplmessage($param);
		}
	}
	public static function storeOrderPay($sid, $orderInfo)
	{
		$orderInfo['pay_time'] = time_format($orderInfo['pay_time']);
		$messagetpl = Config::getconfig('messagetpl');
		$param['template_id'] = $messagetpl['pay_tpl'];

		$param['uuid'] = Store::getUuid($sid);

		$param['uid'] = UuidRelation::getuid($param['uuid']);

		$param['title'] = '店铺订单通知';
		$param['content'] = '您有一个新的订单，请及时处理';
		$param['pages'] = '/pagesA/my/adminstore/orderDetail';
		$param['query'] = 'id=' . $orderInfo['id'];

		$param['data']['character_string1']['value'] = $orderInfo['order_num_alias'];
		$param['data']['thing3']['value'] = mb_substr($orderInfo['pay_subject'], 0, 18);
		$param['data']['time5']['value'] = $orderInfo['pay_time'];
		$param['data']['amount4']['value'] = $orderInfo['total'];

		if (self::send($param)) {
			self::sendmptplmessage($param);
		}
	}
	public static function undertake($orderInfo)
	{
		$orderInfo['pay_time'] = time_format($orderInfo['pay_time']);
		$technicalConfig = Config::getconfig('technical');
		if ($technicalConfig['is_pickuporder'] == 1) {
			$messagetpl = Config::getconfig('messagetpl');
			$param['template_id'] = $messagetpl['undertake_tpl'];
			$Technical = Technical::gelist_bycitycate($orderInfo);

			if (!empty($Technical)) {
				foreach ($Technical as $vo) {
					$param['uid'] = $vo['uid'];

					if (empty($param['uid'])) {
						$param['uid'] = UuidRelation::getuid($vo['uuid']);
					}
					$param['uuid'] = $vo['uuid'];
					$param['title'] = '平台推荐单通知';
					$param['pages'] = '/pagesA/my/publicOrder/order';

					$param['content'] = '您有一个新的平台推荐订单，请及时处理';
					$param['data']['character_string2']['value'] = $orderInfo['order_num_alias'];
					$param['data']['thing4']['value'] = $orderInfo['shipping_name'];
					$param['data']['thing9']['value'] = mb_substr($orderInfo['pay_subject'], 0, 18);
					$param['data']['amount3']['value'] = $orderInfo['total'];
					$param['data']['time5']['value'] = $orderInfo['pay_time'];

					if (self::send($param)) {
						self::sendmptplmessage($param);
					}
				}
			}
		}
	}

	public static function orderPayNoticeAdmin($orderInfo)
	{
		$orderInfo['pay_time'] = time_format($orderInfo['pay_time']);
		$messagetpl = Config::getconfig('messagetpl');
		$param['template_id'] = $messagetpl['pay_tpl'];

		$where['weid'] = weid();
		$where['sid'] = 0;
		$query = Users::where($where);
		$query->where('role_id', 'in', UsersRoles::getadminids());
		$query->whereNotNull('uuid');
		$query->where('uuid', '<>', '');

		$adminlist = $query->select()->toArray();
		//var_dump($query->getLastsql());

		if (!empty($adminlist)) {
			foreach ($adminlist as $vo) {

				$param['uid'] = UuidRelation::getuid($vo['uuid']);
				$param['uuid'] = $vo['uuid'];
				$param['title'] = '订单支付成功';
				$param['content'] = '订单支付成功';

				$param['pages'] = '/pagesA/my/admin/orderDetail';
				$param['query'] = 'id=' . $orderInfo['id'];

				$param['data']['character_string1']['value'] = $orderInfo['order_num_alias'];
				$param['data']['thing3']['value'] = mb_substr($orderInfo['pay_subject'], 0, 20);
				$param['data']['time5']['value'] = $orderInfo['pay_time'];
				$param['data']['amount4']['value'] = $orderInfo['total'];

				if (self::send($param)) {
					self::sendmptplmessage($param);
				}
			}
		}
	}

	public static function orderRefundNoticeAdmin($orderInfo)
	{

		$orderInfo['pay_time'] = time_format($orderInfo['pay_time']);
		$messagetpl = Config::getconfig('messagetpl');
		$param['template_id'] = $messagetpl['refund_tpl'];

		$where['weid'] = weid();
		$where['sid'] = 0;
		$query = Users::where($where);
		$query->where('role_id', 'in', UsersRoles::getadminids());
		$query->whereNotNull('uuid');
		$query->where('uuid', '<>', '');

		$adminlist = $query->select()->toArray();
		//var_dump($query->getLastsql());

		if (!empty($adminlist)) {
			foreach ($adminlist as $vo) {

				$param['uid'] = UuidRelation::getuid($vo['uuid']);
				$param['uuid'] = $vo['uuid'];
				$param['title'] = '用户申请订单退款';
				$param['pages'] = '/pagesA/my/admin/orderDetail';
				$param['content'] = '订单支付成功';
				$param['query'] = 'id=' . $orderInfo['id'];

				$param['data']['number1']['value'] = $orderInfo['order_num_alias'];
				$param['data']['thing2']['value'] = mb_substr($orderInfo['pay_subject'], 0, 20);
				$param['data']['amount4']['value'] = $orderInfo['total'];

				if (self::send($param)) {
					self::sendmptplmessage($param);
				};
			}
		}
	}

	public static function send($param)
	{
		$data['weid'] = weid();
		$data['title'] = $param['title'];
		$data['content'] = $param['content'];
		$data['pages'] = $param['pages'] . '?' . $param['query'];
		$uid = $param['uid'];
		if (empty($uid)) {
			$uid = UuidRelation::getuid($param['uuid']);
		}

		if (!empty($uid)) {
			$res = self::create($data);
			if ($res) {
				MessageBroadcast::create([
					'message_id' => $res->id,
					'sender' => 0,
					'receiver' => $uid,
					'is_read' => 0
				]);
			}
		}

		return $res;
	}
	public static function sendmptplmessage($param)
	{

		if (!empty($param['template_id'])) {
			$weid = weid();
			if (!empty($param['uid'])) {
				$Openid = Openid::getMpOpenidbyuid($param['uid']);
			}
			if (empty($Openid)) {
				$Openid = Openid::getMpOpenidbyuuid($param['uuid']);
			}

			if (!empty($Openid)) {
				$miniConfig = Config::getconfig('miniprogram');
				$app = \app\samos\wechat\Messagetpl::maketpl();

				if (!empty($app)) {

					$checkpages = $param['pages'];
					if ($param['query']) {
						$param['pages'] = $param['pages'] . '?' . $param['query'];
					}

					if (empty($param['data'])) {
						$param['data']['first'] = mb_substr($param['title'], 0, 10);
						$param['data']['remark'] = mb_substr($param['content'], 0, 20);
					}

					if (!empty($miniConfig['app_id']) &&  MiniProgram::ispagethereare($checkpages)) {
						$messagedata = [
							'touser' => $Openid,
							'template_id' => trim($param['template_id']),
							'miniprogram' => [
								'appid' => trim($miniConfig['app_id']),
								'pagepath' => $param['pages'],
							],
							'data' => $param['data'],
						];
					} else {
						$url = gethost() . scriptPath() .  '/h5/?i=' . $weid . '#' . $param['pages'];
						$messagedata = [
							'touser' => $Openid,
							'template_id' => trim($param['template_id']),
							'url' => $url,
							'data' => $param['data'],
						];
					}
					$res = @$app->send($messagedata);
					if ($res) {
						Test::create(['title' => $param['title'] . '发公众号模板消息', 'info' => serialize($res)]);
					}
				}
			}
		}
	}
}
