<?php

namespace app\samos\wechat;

use EasyWeChat\Factory;
use app\model\Config;
use app\model\Paymethod;

class WxPaymethod
{
	static public function makepay($ptype = 'wxapp')
	{

		if (empty($ptype)) {
			$ptype = 'wxapp';
		}
		if ($ptype == 'wxapp') {
			$app_id = Config::getconfig('miniprogram')['app_id'];
		} elseif ($ptype == 'mp') {
			$app_id = Config::getconfig('mp')['app_id'];
		}

		$wxpay_settings = Paymethod::getwx_settings();

		$app = Factory::payment([
			'app_id'             => trim($app_id),
			'mch_id'             => trim($wxpay_settings['mchid']),
			'key'                => trim($wxpay_settings['signkey']),
			'cert_path'          => dirname(dirname(dirname(__DIR__))) . trim($wxpay_settings['cert_path']), // XXX: 绝对路径！！！！
			'key_path'           => dirname(dirname(dirname(__DIR__))) . trim($wxpay_settings['key_path'])  // XXX: 绝对路径！！！！
		]);
		$appdata['lan'] = Author()::getlan();
		if ($wxpay_settings['service_pay'] == 1 && !empty(trim($wxpay_settings['sub_mch_id']))) {
			$app->setSubMerchant(trim($wxpay_settings['sub_mch_id']));
		}

		return $app;
	}

	static public function makepayv3($ptype = 'wxapp')
	{

		include dirname(dirname(dirname(__DIR__))) . '/extend/WeChatDeveloper/include.php';
		if (empty($ptype)) {
			$ptype = 'wxapp';
		}
		if ($ptype == 'wxapp') {
			$app_id = Config::getconfig('miniprogram')['app_id'];
		} elseif ($ptype == 'mp') {
			$app_id = Config::getconfig('mp')['app_id'];
		}

		$wxpay_settings = Paymethod::getwx_settings();
		$app = \WePayV3\Transfers::instance(
			[
				// 可选，公众号APPID
				'appid' => trim($app_id),
				// 必填，微信商户编号ID
				'mch_id'  => trim($wxpay_settings['mchid']),
				// 必填，微信商户V3接口密钥
				'mch_v3_key'  => trim($wxpay_settings['signkeyv3']),
				// 可选，微信商户证书序列号，可从公钥中提取
				'cert_serial' => '',
				// 必填，微信商户证书公钥，支持证书内容或文件路径
				'cert_public' => dirname(dirname(dirname(__DIR__))) . trim($wxpay_settings['cert_path']),
				// 必填，微信商户证书私钥，支持证书内容或文件路径
				'cert_private' => dirname(dirname(dirname(__DIR__))) . trim($wxpay_settings['key_path']),
				// 可选，运行时的文件缓存路径
				'cache_path'  => ''
			]
		);
		$appdata['lan'] = Author()::getlan();
		return $app;
	}
}
