<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Users;
use app\model\Member;
use app\model\UuidRelation;
use app\model\Openid;
use app\model\MessageSms;
use app\model\Technical;
use app\model\Operatingcity;
use app\model\Store;

class LoginController extends Base
{
	//用户登录
	public function index()
	{
		$data = input('post.');
		$ptype = input('post.ptype', '', 'serach_in');
		if ($data['username'] == 'undefined') {
			$data['username'] = '';
		}
		if ($data['password'] == 'undefined') {
			$data['password'] = '';
		}
		if ($data['userphone'] == 'undefined') {
			$data['userphone'] = '';
		}
		if ($data['captcha'] == 'undefined') {
			$data['captcha'] = '';
		}
		if (empty($data['username'])) {
			$data['username'] = $data['tel'];
		}
		$data['username'] = trim($data['username']);

		if (!empty($data['userphone'])) {

			if (empty($data['captcha'])) {
				throw new ValidateException('请输入验证');
			}
			$res = $this->checkphonecaptcha($data);
		} else {

			if (empty($data['username'])) {
				throw new ValidateException('请输入用户名');
			}

			if (empty($data['password'])) {
				throw new ValidateException('请输入密码');
			}
			$res = $this->checkLogin($data);
		}

		if ($res) {
			$this->userInfo['uuid'] = $res['uuid'];
			$this->setAppToken($this->userInfo, $this->getAppToken());
			$uuid = UuidRelation::getuuidbyid($res, $ptype);
			UuidRelation::create(['weid' => weid(), 'ptype' => $ptype, 'uuid' => $uuid, 'uid' => UID()]);
			return $this->json(['msg' => '登录成功', 'data' => $res]);
			Users::where('id', $res['id'])->update(['login_time' => time(), 'login_ip' => client_ip()]);
		}
	}
	//获取小程序手机号
	public function getphonenumber()
	{
		$code = input('post.code', '', 'serach_in');
		$phonedata = \app\samos\wechat\MiniProgram::getphonenumber($code);
		$data = Member::bindphonenumber($phonedata->phoneNumber);

		$phone = $this->logintype($phonedata->phoneNumber);
		if ($phone && $phone['status'] == 1) {
			$data['is_reg'] = 0;
		} else {
			$data['is_reg'] = 1;
		}

		return $this->json(['data' => $data]);
	}

	//验证登录
	private function checkLogin($data)
	{
		$where['username'] = $data['username'];
		$info =	Users::where($where)->find();

		if (empty($info)) {
			$uuidarray = $this->logintype($data['username']);
			if ($uuidarray['uuid']) {
				$info =	Users::where('uuid', $uuidarray['uuid'])->find();
			}
		}

		if ($info) {
			$info = $info->toArray();
			if ($info['password'] === pass_hash($data['password'], $info["salt"])) {
				unset($info['password']);
				unset($info["salt"]);

				if (empty($info['uuid'])) {
					$info['uuid'] = uniqid(rand(1, 10000));
					Users::where('id', $info['id'])->update(['uuid' => $info['uuid']]);
				}

				if (!($info['status'])) {
					throw new ValidateException("该账户被禁用");
				}
			} else {
				throw new ValidateException("用户名或者密码不正确");
			}
		} else {
			throw new ValidateException("用户名不存在");
		}

		return $info;
	}
	//验证登录
	private function checkphonecaptcha($data)
	{

		if ($data['captcha'] == $this->userInfo['captcha']) {
			$info = Member::bindphonenumber($data['userphone']);
			$phone = $this->logintype($data['userphone']);
			if ($phone && $phone['status'] == 1) {
				$info['is_reg'] = 0;
			} else {
				$info['is_reg'] = 1;
			}
			$from = input('get.from', '', 'serach_in');
			if ($from != 'wxapp' && $from != 'mp') {
				$this->userInfo['openid'] = $data['userphone'];
			}
		} else {
			throw new ValidateException("验证码不正确" . $this->getAppToken() . 'dssssss');
		}
		return $info;
	}
	//手机短信验证码
	public function captcha()
	{
		$phone = input('post.phone', '', 'serach_in');
		if (!empty($phone)) {
			$this->userInfo['captcha'] = rand(1111, 9999);
			$data = MessageSms::send_sms([
				'phone' => $phone,
				'param' => json_encode([
					'code' => $this->userInfo['captcha']
				])
			]);
			$this->setAppToken($this->userInfo, $this->getAppToken());
		}
		return $this->json(['msg' => '手机验证码发送成功', 'data' => $data]);
	}
	//用户退出登录
	public function logout()
	{
		$ptype = input('post.ptype', '', 'serach_in');
		if ($ptype) {
			$where['weid'] = weid();
			$where['ptype'] = $ptype;
			$where['uid'] = UID();
			$res = UuidRelation::where($where)->delete();
			//$sql = $res->getLastsql();
			$this->userInfo[$ptype] = '';
			$this->setAppToken($this->userInfo, $this->getAppToken());
		}
		return $this->json(['msg' => '退出成功', 'res' => $res]);
	}


	//手机登录
	public function logintype($tel)
	{
		$ptype = input('post.ptype', '', 'serach_in');
		if ($ptype == 'technical') {
			$res = Technical::where('tel', $tel)->find();
			if ($res) {
				$res = $res->toArray();
			}
		}
		if ($ptype == 'operatingcity') {
			$res = Operatingcity::where('tel', $tel)->find();
			if ($res) {
				$res = $res->toArray();
			}
		}
		if ($ptype == 'store') {
			$res = Store::where('tel', $tel)->find();
			if ($res) {
				$res = $res->toArray();
			}
		}
		if ($ptype == 'member') {
			$res['status'] = 1;
		}
		return $res;
	}
}
