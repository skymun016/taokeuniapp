<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Openid;
use app\samos\wechat\Wechatmp;
use function Qiniu\entry;

class WechatmpController extends Base
{

	public function wechat()
	{
		$backurl = input('param.backurl');

		$app =  Wechatmp::makemp();
		if (!empty($app)) {
			$oauth = $app->oauth;

			//回调地址
			$callback_url = gethttpshost() . $_SERVER["SCRIPT_NAME"] . '?from=mp&s=/index/wechatmp/wechatres&i=' . weid() . '&xmtoken=' . $this->getAppToken() . '&uid=' . input('param.uid') . '&backurl=' . urlencode($backurl);
			// 将响应输出
			$redirectUrl = $oauth->scopes(['snsapi_base'])->redirect($callback_url);
			header("Location: {$redirectUrl}");
		} else {
			$this->eothtmlmsg();
		}
	}
	public function wechatuserinfo()
	{
		$backurl = input('param.backurl');

		$app =  Wechatmp::makemp();

		if (!empty($app)) {
			$oauth = $app->oauth;

			//回调地址
			$callback_url = gethttpshost() . $_SERVER["SCRIPT_NAME"] . '?from=mp&s=/index/wechatmp/wechatres&i=' . weid() . '&xmtoken=' . $this->getAppToken() . '&uid=' . input('param.uid') . '&backurl=' . urlencode($backurl);

			$redirectUrl = $oauth->scopes(['snsapi_userinfo'])->redirect($callback_url);

			// 将响应输出
			header("Location: {$redirectUrl}");
		} else {
			$this->eothtmlmsg();
		}
	}

	public function wechatres()
	{

		$app = Wechatmp::makemp();
		if (!empty($app)) {
			$oauth = $app->oauth;

			$code = input('get.code');
			$user = $oauth->userFromCode($code);
			//print_r($user);}
			$uid = input('get.uid', '', 'serach_in');
			if ($uid == 'undefined' || $uid == 'null') {
				$uid = '';
			}
			$xmtoken = input('get.xmtoken', '', 'serach_in');
			$openid = $user->getId();

			if (empty($openid)) {
				$openid = $user['token_response']['openid'];
			}

			if (empty($openid)) {
				return $this->json(['msg' => 'openid获取不成功', 'data' => $user]);
			}
			if (!empty($uid)) {
				Openid::addupdate([
					'openid' => $openid,
					'ptype' => 'mp',
					'uid' => $uid
				]);
			}
			$data = $this->userInfo;

			$data['ptype'] = 'mp';
			$data['openid'] = $openid;
			$data['nickname'] = $user->getNickname();
			$data['avatar'] =  $user->getAvatar();
			$data['upnickname'] = $user->getNickname();
			$data['upavatar'] =  $user->getAvatar();
			$data['weid'] = weid();
			$this->setAppToken($data, $xmtoken);
			$backurl = input('get.backurl');
			$backurl = str_replace("xmtokenvalue", $xmtoken, $backurl);
			return redirect($backurl);
		} else {
			$this->eothtmlmsg();
		}
	}

	public function getopenid()
	{
		$backurl = input('param.backurl');

		$app =  Wechatmp::makemp();
		if (!empty($app)) {
			$oauth = $app->oauth;

			//回调地址
			$callback_url = gethttpshost() . $_SERVER["SCRIPT_NAME"] . '?from=mp&s=/index/wechatmp/getopenidres&i=' . weid() . '&xmtoken=' . $this->getAppToken() . '&uid=' . input('param.uid', '', 'serach_in') . '&backurl=' . urlencode($backurl);
			$redirectUrl = $oauth->scopes(['snsapi_base'])->redirect($callback_url);
			header("Location: {$redirectUrl}");
		} else {
			$this->eothtmlmsg();
		}
	}
	public function getopenidres()
	{

		$app = Wechatmp::makemp();
		if (!empty($app)) {
			$oauth = $app->oauth;
			// 获取 OAuth 授权结果用户信息
			$code = input('get.code');
			$user = $oauth->userFromCode($code);
			$openid = $user->getId();

			if (empty($openid)) {
				$openid = $user['token_response']['openid'];
			}

			$backurl = input('get.backurl');

			$backurl = str_replace('#/', '', $backurl);
			if (strpos($backurl, '?') !== false) {
				$backurl = $backurl . '&openid=' . $openid;
			} else {
				$backurl = $backurl . '?openid=' . $openid;
			}

			return redirect($backurl);
		} else {
			$this->eothtmlmsg();
		}
	}

	function eothtmlmsg()
	{
		echo <<< EOT
			<!DOCTYPE html>
			<html lang="zh-cmn-Hans">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
				<title>公众号配置不正确</title>
				<link rel="stylesheet" href="static/style/weui.css"/>
			</head>
			<body ontouchstart data-weui-mode="care" >
				<div class="weui-msg">
					<div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
					<div class="weui-msg__text-area">
						<h2 style="text-align: center;" class="weui-msg__title">公众号配置不正确</h2>
					</div>
				</div>
			</body>
			</html>
EOT;
	}
}
