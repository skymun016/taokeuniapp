<?php

namespace app\samos\wechat;

class SubscribeMessage

{
	static function addparam($tpl)
	{
		//支付成功通知
		$data['pay_tpl']['title'] = '支付成功通知';
		$data['pay_tpl']['tid'] = 1221;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['pay_tpl']['kidList'] = [1, 2, 3, 4, 6];
		$data['pay_tpl']['sceneDesc'] = '通知用户订单已支付成功';   // 服务场景描述，非必填

		//退款申请通知
		$data['refund_tpl']['title'] = '退款申请通知';
		$data['refund_tpl']['tid'] = 1150;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['refund_tpl']['kidList'] = [1, 2, 3, 4, 8];
		$data['refund_tpl']['sceneDesc'] = '通知用户订单退款成功';   // 服务场景描述，非必填

		//服务派单通知
		$data['staff_tpl']['title'] = '服务派单通知';
		$data['staff_tpl']['tid'] = 2411;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['staff_tpl']['kidList'] = [3, 8, 12];
		$data['staff_tpl']['sceneDesc'] = '通知用户订单服务派单通知';   // 服务场景描述，非必填

		//尾款支付通知
		$data['itional_tpl']['title'] = '尾款支付通知';
		$data['itional_tpl']['tid'] = 5430;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['itional_tpl']['kidList'] = [1, 4, 6];
		$data['itional_tpl']['sceneDesc'] = '通知用户尾款支付';   // 服务场景描述，非必填

		//服务完成通知
		$data['complete_tpl']['title'] = '服务完成通知';
		$data['complete_tpl']['tid'] = 2260;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['complete_tpl']['kidList'] = [3, 1, 6, 11, 7];
		$data['complete_tpl']['sceneDesc'] = '通知用户服务完成';   // 服务场景描述，非必填

		//订单发货通知
		$data['send_tpl']['title'] = '订单发货通知';
		$data['send_tpl']['tid'] = 4777;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['send_tpl']['kidList'] = [1, 6, 8, 9, 10];
		$data['send_tpl']['sceneDesc'] = '通知用户订单发货';   // 服务场景描述，非必填


		return $data[$tpl];
	}
}
