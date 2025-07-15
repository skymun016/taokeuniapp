<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Member;
use app\model\Users;
use app\model\Agent;
use app\model\Curl;
use app\model\Config;
use app\model\CouponReceive;
use app\model\Technical;
use app\model\Openid;
use app\model\OrderStaff;
use app\model\kefu\Seating;
use app\model\Coupon;

class MemberController extends Base
{
	public function detail()
	{
		$memberConfig = Config::getconfig('member');
		$this->updatanickname();
		$Membermob = new Member;
		$memberinfo = $Membermob->getUserByWechat();

		$memberinfo['is_agent'] =  (int) Agent::is_agent($memberinfo['id']);

		$user_id = $memberinfo['id'];
		if ($memberinfo['primaryuid']) {
			$user_id = $memberinfo['primaryuid'];
		}

		$user = Users::getuserbyuid($user_id);

		if (!empty($user)) {
			$memberinfo['is_user'] = 1;
		} else {
			$memberinfo['is_user'] = 0;
		}
		if (!empty($user['sid'])) {
			$memberinfo['is_storeadmin'] = 1;
		} else {
			$memberinfo['is_storeadmin'] = 0;
		}

		$Agent = Agent::where(['weid' => weid(), 'uid' => $user_id])->find();
		if (!empty($Agent)) {
			$memberinfo['agent'] = $Agent->toArray();
		} else {
			$memberinfo['agent']['income'] = 0;
		}

		$Technical = Technical::where(['weid' => weid(), 'uid' => $user_id])->find();
		if (!empty($Technical)) {
			$Technical = $Technical->toArray();
		}

		if (!empty($Technical)) {
			$memberinfo['is_technical'] = $Technical['status'];
		} else {
			$memberinfo['is_technical'] = 0;
		}

		$memberinfo['coupon'] = (int) CouponReceive::where(['weid' => weid(), 'uid' => $memberinfo['id']])->count();

		$memberinfo['sex'] = sex($memberinfo['sex']);
		$memberinfo['is_submitaudit'] = \app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'));

		if ($memberConfig['is_wxnickname'] == 1 && $memberinfo['nickname'] == '用户_' . $memberinfo['id']) {
			$memberinfo['is_online'] = 0;
			if (!empty($memberinfo['userpic'])) {
				$memberinfo['is_online'] = 1;
			}
			if (!empty($memberinfo['telephone'])) {
				$memberinfo['is_online'] = 1;
			}
		} else {
			$memberinfo['is_online'] = 1;
		}

		$data = $memberinfo;

		return $this->json(['data' => $data]);
	}

	public function updatanickname()
	{

		if (!empty($this->userInfo['upnickname'])) {
			$postdata['nickname'] =  removeEmoji($this->userInfo['upnickname']);
			$this->userInfo['upnickname'] = "";
		}

		if (!empty($this->userInfo['upavatar'])) {
			$postdata['userpic'] =  $this->userInfo['upavatar'];
			$this->userInfo['upavatar'] = "";
		}

		if (!empty($postdata['nickname'])) {
			Member::where('id', UID())->update($postdata);
			$this->setAppToken($this->userInfo, $this->getAppToken());
		}
	}

	public function getkefuurl()
	{
		$orderid = input('get.orderid', '', 'serach_in');

		$uuid = OrderStaff::getuuid($orderid);
		$Membermob = new Member;
		$memberinfo = $Membermob->getUserByWechat();
		$data['url'] = gethost() . scriptPath() . TP_APIURL . '/h5-im?toid=' . Openid::getMpOpenidbyuuid($uuid) . '&uid=' . $memberinfo['id'] . '&xmtoken=' . $this->getAppToken();

		return $this->json(['data' => $data]);
	}
	public function getsetmpurl()
	{
		$Membermob = new Member;
		$memberinfo = $Membermob->getUserByWechat();

		$data['url'] = gethttpshost() . scriptPath() . TP_APIURL . '?s=/index/member/setmpopenid&xmtoken=' . $this->getAppToken() . '&uid=' . $memberinfo['id'];

		return $this->json(['data' => $data]);
	}

	public function getmpopenid()
	{
		$Membermob = new Member;
		$memberinfo = $Membermob->getUserByWechat();

		$Openidmob = Openid::where(['weid' => weid(), 'ptype' => 'mp', 'uid' => $memberinfo['id']])->find();
		if ($Openidmob) {
			return $this->json(['data' => $Openidmob->openid]);
		}
	}

	public function setseatingopenid()
	{
		$openid = input('get.openid', '', 'serach_in');

		$backurl = gethttpshost() . $_SERVER["SCRIPT_NAME"] . '?s=/index/member/setseatingopenid&xmtoken=' . $this->getAppToken() . '&seaid=' . input('get.seaid', '', 'serach_in') . '&i=' . input('get.i', '', 'serach_in');
		if (empty($openid)) {
			$url = gethttpshost() . $_SERVER["SCRIPT_NAME"] . '?s=/index/wechatmp/getopenid&xmtoken=' . $this->getAppToken() . '&i=' . input('get.i', '', 'serach_in') . '&backurl=' . urlencode($backurl);
			return redirect($url);
		} else {
			$seaid = input('get.seaid', '', 'serach_in');
			Seating::where('id', $seaid)->update(['chatid' => $openid]);
		}
		echo <<< EOT
			<!DOCTYPE html>
			<html lang="zh-cmn-Hans">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
				<title>绑定公众号信息提醒</title>
				<link rel="stylesheet" href="static/style/weui.css"/>
			</head>
			<body ontouchstart data-weui-mode="care" >
				<div class="weui-msg">
					<div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
					<div class="weui-msg__text-area">
						<h2 style="text-align: center;" class="weui-msg__title">绑定客服成功</h2>
					</div>
				</div>
			</body>
			</html>
EOT;
	}

	public function setmpopenid()
	{
		$weid = weid();
		$openid = input('get.openid', '', 'serach_in');

		$backurl = gethttpshost() . $_SERVER["SCRIPT_NAME"] . '?s=/index/member/setmpopenid&xmtoken=' . $this->getAppToken() . '&uid=' . input('get.uid', '', 'serach_in');
		if (empty($openid)) {
			$url = gethttpshost() . $_SERVER["SCRIPT_NAME"] . '?s=/index/wechatmp/getopenid&xmtoken=' . $this->getAppToken() . '&uid=' . input('get.uid', '', 'serach_in') . '&backurl=' . urlencode($backurl);
			return redirect($url);
		} else {
			$uid = input('get.uid', '', 'serach_in');
			Openid::addupdate([
				'openid' => $openid,
				'ptype' => 'mp',
				'uid' => $uid
			]);
			echo <<< EOT
			<!DOCTYPE html>
			<html lang="zh-cmn-Hans">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
				<title>绑定公众号信息提醒</title>
				<link rel="stylesheet" href="static/style/weui.css"/>
			</head>
			<body ontouchstart data-weui-mode="care" >
				<div class="weui-msg">
					<div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
					<div class="weui-msg__text-area">
						<h2 style="text-align: center;" class="weui-msg__title">设置成功</h2>
					</div>
				</div>
			</body>
			</html>
EOT;
		}
	}

	public function openid()
	{
		$code = input('get.code', '', 'serach_in');
		$from = input('get.from', '', 'serach_in');

		if ($code && $from == 'wxapp') {
			$app = \app\samos\wechat\MiniProgram::makemini();
			$data = $app->auth->session($code);
		} else {
			$data = $this->userInfo;
		}
		$data['ptype'] = $from;
		$data['sessionid'] = md5(uniqid());
		$data['weid'] = weid();

		$this->setAppToken($data, $data['sessionid']);

		return $this->json(['data' => $data]);
	}
	public function check()
	{
		$from = input('get.from', '', 'serach_in');
		$errno = 0;

		if ($from == 'wxapp' && empty($this->userInfo['openid'])) {
			$errno = 1;
		}
		if ($from == 'mp' && empty($this->userInfo['openid'])) {
			$errno = 1;
		}

		return $this->json(['errno' => $errno, 'data' => $this->userInfo]);
	}
	public function userinfo()
	{
		return $this->json(['data' => $this->userInfo]);
	}

	public function getuid()
	{
		$data['uid'] = UID();
		return $this->json(['data' => $data]);
	}
	//2023-8-9已废弃
	public function getphonenumber()
	{
		$code = input('post.code', '', 'serach_in');
		$phonedata = \app\samos\wechat\MiniProgram::getphonenumber($code);
		$data = Member::bindphonenumber($phonedata->phoneNumber);

		return $this->json(['data' => $data]);
	}

	public function bindpid()
	{
		$pid = input('post.pid', '', 'serach_in');

		if (empty($pid)) {
			$pid = input('get.pid', '', 'serach_in');
		}

		if (!empty($pid) && !empty(UID())) {
			$Membermob = new Member;
			$memberinfo = $Membermob->getUserByWechat();

			if (empty($memberinfo['pid'])) {
				if ($memberinfo['id'] != $pid) {
					Member::where('id', UID())->update(['pid' => $pid]);
				}
			}
		}
		return $this->json(['data' => $memberinfo]);
	}

	public function team()
	{
		$data['first'] = Member::field('id,nickname,regdate,userpic')
			->where(['weid' => weid(), 'pid' => UID()])
			->select()
			->toArray();

		if (!empty($data['first'])) {
			foreach ($data['first'] as &$vo) {
				$vo['regdate'] = time_format($vo['regdate']);

				if (empty($pid)) {
					$pid = $vo['id'];
				} else {
					$pid = $pid . ',' . $vo['id'];
				}
			}
			$pidin = explode(',', $pid);

			$data['second'] = Member::field('id,nickname,regdate,userpic')
				->where(['weid' => weid(), 'pid' => $pidin])
				->select()
				->toArray();

			foreach ($data['second'] as &$vo) {
				$vo['regdate'] = time_format($vo['regdate']);
			}
		}

		return $this->json(['data' => $data]);
	}

	public function agent()
	{
		$user = Users::getuserbyuid(PUID());
		$data = Agent::field('id,uid,name,tel')
			->where(['weid' => weid(), 'uid' => $user['id']])
			->select()
			->toArray();

		return $this->json(['data' => $data]);
	}

	public function update()
	{
		$uid = UID();
		$postdata = input('post.');

		if (!empty($postdata['userpic'])) {
			$data['userpic'] = $postdata['userpic'];
		}

		if (!empty($postdata['nickname'])) {
			$data['nickname'] = removeEmoji($postdata['nickname']);
		}

		if (!empty($postdata['sex'])) {

			if ($postdata['sex'] == '男') {
				$data['sex'] = 1;
			} elseif ($postdata['sex'] == '女') {
				$data['sex'] = 2;
			} else {
				$data['sex'] = 0;
			}
		}
		$data['id'] = $uid;
		Member::update($data);
		return $this->json(['data' => $data]);
	}
	public function login()
	{
		$Configdata = Config::getconfig('member');
		$Membermob = new Member;
		$memberinfo = $Membermob->getUserByWechat();
		if ($Configdata['index_is_login'] == 1 && !\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {

			if (!empty($memberinfo['telephone'])) {
				if (empty($memberinfo['status'])) {
					$data['errno'] = 20001;
					return $this->json(['msg' => '账号审核中', 'data' => $data]);
				}
			} else {
				$data['errno']  = 10001;
				return $this->json(['msg' => '您没还完成注册', 'data' => $data]);
			}
		}
		$data['uid'] = $memberinfo['id'];

		//新人券
		if (!empty($data['uid'])) {
			$Coupondata = Coupon::where(['weid' => weid(), 'ptype' => 3])->select()->toArray();
			foreach ($Coupondata as $cvo) {
				$CouponReceive = CouponReceive::where(['uid' => $data['uid'], 'ptype' => 3, 'coupon_id' => $cvo['id']])->find();
				if (empty($CouponReceive)) {
					$cvo['coupon_id'] = $cvo['id'];
					$cvo['uid'] = UID();
					unset($cvo['id']);
					unset($cvo['create_time']);
					unset($cvo['update_time']);

					$r = CouponReceive::create($cvo);
					if ($r) {
						$receive_num = $cvo['receive_num'] + 1;
						Coupon::update(['id' => $cvo['coupon_id'], 'receive_num' => $receive_num]);
					}
				}
			}
		}
		$data['errno'] = 0;
		return $this->json(['data' => $data]);
	}
	public function checktelephone()
	{
		$memberConfig = Config::getconfig('member');

		$Membermob = new Member;
		$memberinfo = $Membermob->getUserByWechat();

		if ($memberConfig['is_phone'] == 1 && empty($memberinfo['telephone'])) {
			$memberinfo['is_gettelephone'] = 0;
			$memberinfo['is_online'] = 0; //2023-9-9已废弃
		} else {
			$memberinfo['is_gettelephone'] = 1;
			$memberinfo['is_online'] = 1; //2023-9-9已废弃
		}

		$data = $memberinfo;

		return $this->json(['data' => $data]);
	}
}
