<?php

namespace app\samos\wechat;
use app\model\Config;
include dirname(dirname(dirname(__DIR__))) . '/extend/WeChatDeveloper/include.php';
class Messagetpl

{
	static function maketpl()
	{
		$mpconfig = Config::getconfig('mp');
		$appdata['lan'] = Author()::getlan();
		if (!empty($mpconfig['app_id']) && !empty($mpconfig['secret'])) {
			$WeChatMP = [
				'token'  => trim($mpconfig['token']),   // Token
				'appid' => trim($mpconfig['app_id']),
				'appsecret' => trim($mpconfig['secret']),
				'encodingaeskey' => trim($mpconfig['aes_key']), // EncodingAESKey，兼容与安全模式下请一定要填写！！！
				// 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
				'response_type' => 'array',
				//...
			];
			$wechat = new \WeChat\Template($WeChatMP);
			return $wechat;
		}
	}

	static function addparam($tpl)
	{
		//订单支付成功通知
		$data['pay_tpl']['title'] = '订单支付成功通知';
		$data['pay_tpl']['tid'] = 50096;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['pay_tpl']['keyword_name_list'] = ["订单号","服务产品","支付时间","支付金额"];
		$data['pay_tpl']['sceneDesc'] = '订单支付成功通知';   // 服务场景描述，非必填

		//退款申请审批通知
		$data['refund_tpl']['title'] = '退款申请审批通知';
		$data['refund_tpl']['tid'] = 47632;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['refund_tpl']['keyword_name_list'] =["订单编号","服务名称","退款金额"];
		$data['refund_tpl']['sceneDesc'] = '通知用户订单退款成功';   // 服务场景描述，非必填

		//服务派单通知
		$data['undertake_tpl']['title'] = '新订单通知';
		$data['undertake_tpl']['tid'] = 42995;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['undertake_tpl']['keyword_name_list'] = ["订单编号","客户姓名","服务名称","订单金额","下单时间"];
		$data['undertake_tpl']['sceneDesc'] = '通知订单服务派单通知';   // 服务场景描述，非必填

		//平台派单/师傅接单
		$data['technical_tpl']['title'] = '客户预约订单处理提醒';
		$data['technical_tpl']['tid'] = 45369;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['technical_tpl']['keyword_name_list'] = ["订单编号","服务名称","订单金额","客户名称","联系电话"];
		$data['technical_tpl']['sceneDesc'] = '通知服务订单已分配';   // 服务场景描述，非必填

		//客服
		$data['kefu_tpl']['title'] = '意见反馈通知';
		$data['kefu_tpl']['tid'] = 45426;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['kefu_tpl']['keyword_name_list'] = ["姓名","反馈问题"];
		$data['kefu_tpl']['sceneDesc'] = '客户咨询';   // 服务场景描述，非必填

		return $data[$tpl];
	}
}
