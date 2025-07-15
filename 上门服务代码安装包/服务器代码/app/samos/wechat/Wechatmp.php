<?php

namespace app\samos\wechat;

use EasyWeChat\Factory;
use app\model\Config;

class Wechatmp
{
	static function makemp()
	{
		$mpconfig = Config::getconfig('mp');
		$appdata['lan'] = Author()::getlan();
		if (!empty($mpconfig['app_id']) && !empty($mpconfig['secret'])) {
			$WeChatMP = [
				'app_id' => trim($mpconfig['app_id']),
				'secret' => trim($mpconfig['secret']),
				'token'  => trim($mpconfig['token']),   // Token
				'aes_key' => trim($mpconfig['aes_key']), // EncodingAESKey，兼容与安全模式下请一定要填写！！！
				// 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
				'response_type' => 'array',
				//...
			];
			return Factory::officialAccount($WeChatMP);
		}
	}

}
