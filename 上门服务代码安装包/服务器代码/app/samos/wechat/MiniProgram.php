<?php

namespace app\samos\wechat;

use EasyWeChat\Factory;
use app\model\Config;
use app\model\Test;

class MiniProgram
{

	static function makemini()
	{

		$Configdata = Config::getconfig('miniprogram');
		$appdata['lan'] = Author()::getlan();

		$t = input('get.t', '', 'serach_in');
		if (empty($t)) {
			if (!empty($Configdata['app_id']) && !empty($Configdata['secret'])) {
				return Factory::miniProgram([
					'app_id' => trim($Configdata['app_id']),
					'secret' => trim($Configdata['secret'])
				]);
			}
		} else {
			if (!empty($Configdata['techapp_id']) && !empty($Configdata['techsecret'])) {
				return Factory::miniProgram([
					'app_id' => trim($Configdata['techapp_id']),
					'secret' => trim($Configdata['techsecret'])
				]);
			}
		}
	}

	static function getQrcode($scene, $page, $width = 230)
	{

		$app =  self::makemini();
		if (!empty($app)) {
			$response = $app->app_code->getUnlimit($scene, [
				'page'  => $page,
				'width' => $width,
			]);

			if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
				$filename = $response->save(config('filesystem.disks.public.root'), 'qrcode' . md5(base64_encode(time())) . '.jpg');
			}

			$ifw7ppath = dirname(dirname(dirname(dirname(__DIR__))));
			$ifw7ppatharray =  explode('/', $ifw7ppath);

			if (end($ifw7ppatharray) == 'addons') {
				$w7ppath = 'addons/' . config('database.app_name') . '/';
			} else {
				$w7ppath = '';
			}

			$appdata['lan'] = Author()::getlan();
			return toimg($w7ppath . 'public/uploads/' . $filename);
		}
	}

	static function getQrcode2($page, $width = 230)
	{

		$app =  self::makemini();
		if (!empty($app)) {
			$response = $app->app_code->getQrCode($page, $width);

			if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
				$filename = $response->save(config('filesystem.disks.public.root'), 'qrcode' . md5(base64_encode(time())) . '.jpg');
			}

			$ifw7ppath = dirname(dirname(dirname(dirname(__DIR__))));
			$ifw7ppatharray =  explode('/', $ifw7ppath);

			if (end($ifw7ppatharray) == 'addons') {
				$w7ppath = 'addons/' . config('database.app_name') . '/';
			} else {
				$w7ppath = '';
			}

			$appdata['lan'] = Author()::getlan();
			return toimg($w7ppath . 'public/uploads/' . $filename);
		}
	}

	//判断是否存在页面
	static function ispagethereare($page)
	{
		//判断如果第一个字符是“/”，则去掉
		$firststr = mb_substr($page, 0, 1);
		if ($firststr == '/') {
			$page = mb_substr($page, 1);
		}
		$app =  self::makemini();
		if (!empty($app)) {
			$response = $app->app_code->getUnlimit('i', [
				'page'  => $page,
				'width' => 200,
			]);
		}
		//Test::create(['title' => $page . '测试判断是否存在页面', 'info' => serialize($response)]);
		if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
			return true;
		} else {
			return false;
		}
	}

	static function urllink($param)
	{

		$app = self::makemini();
		if (!empty($app)) {
			$url = 'https://api.weixin.qq.com/wxa/generate_urllink?access_token=' . $app->access_token->getToken(true)['access_token'];

			$retlink = urlpost($url, $param);

			$appdata['lan'] = Author()::getlan();
			return $retlink;
		}
	}

	static function createLiveRoom($data)
	{

		$create_data = [
			'name' => '',  // 房间名字
			'coverImg' => '',   // 通过 uploadfile 上传，填写 mediaID
			'startTime' => 0,   // 开始时间
			'endTime' => 0, // 结束时间
			'anchorName' => '',  // 主播昵称
			'anchorWechat' => '',  // 主播微信号
			'shareImg' => '',  //通过 uploadfile 上传，填写 mediaID
			'feedsImg' => '',   //通过 uploadfile 上传，填写 mediaID
			'isFeedsPublic' => 1, // 是否开启官方收录，1 开启，0 关闭
			'type' => 1, // 直播类型，1 推流 0 手机直播
			'screenType' => 0,  // 1：横屏 0：竖屏
			'closeLike' => 0, // 是否 关闭点赞 1 关闭
			'closeGoods' => 0, // 是否 关闭商品货架，1：关闭
			'closeComment' => 0, // 是否开启评论，1：关闭
			'closeReplay' => 1, // 是否关闭回放 1 关闭
			'closeShare' => 0,   //  是否关闭分享 1 关闭
			'closeKf' => 0 // 是否关闭客服，1 关闭
		];
		$params = array_merge($create_data, $data);

		$app = self::makemini();
		if (!empty($app)) {
			$params['coverImg'] = $app->media->uploadImage(localpic($params['coverImg']))['media_id'];
			$params['shareImg'] = $app->media->uploadImage(localpic($params['shareImg']))['media_id'];
			$params['feedsImg'] = $params['coverImg'];

			$url = 'https://api.weixin.qq.com/wxaapi/broadcast/room/create?access_token=' . $app->access_token->getToken(true)['access_token'];

			$res = urlpost($url, $params);
			$appdata['lan'] = Author()::getlan();
			return $res;
		}
	}

	static function getphonenumber($code)
	{

		$param['code'] = $code;
		$app = self::makemini();
		if (!empty($app)) {
			$access_token = $app->access_token->getToken(true)['access_token'];

			$url = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=' . $access_token;

			$phonenumberdate = urlpost($url, $param);

			if ($phonenumberdate['errmsg'] == 'ok') {
				$date = $phonenumberdate['phone_info'];
			}
			$appdata['lan'] = Author()::getlan();
		}
		return $date;
	}
	static function subscribemessage($tpl)
	{

		//订单支付
		$data['pay_tpl']['tid'] = 1221;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['pay_tpl']['kidList'] = [1, 2, 3, 4, 6];
		$data['pay_tpl']['sceneDesc'] = '通知用户订单已支付成功';   // 服务场景描述，非必填
		$appdata['lan'] = Author()::getlan();

		return $data[$tpl];
	}
}
