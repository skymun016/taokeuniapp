<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Users;
use app\model\UsersRoles;
use app\model\Config;
use app\model\Platform;
use tidy;

class LoginController extends Base
{
	//用户登录
	public function index()
	{
		//$data = only('username,password,key');
		$data['username'] = input('post.username', '', 'trim');
		$data['password'] = input('post.password', '', 'trim');
		$data['key'] = input('post.key', '', 'trim');

		if (empty($data['username'])) {
			throw new ValidateException('请输入用户名');
		}

		if (empty($data['password'])) {
			throw new ValidateException('请输入密码');
		}

		$res = $this->checkLogin($data);

		if ($res) {
			$token = $this->setToken($res);
			$res['token'] = $token;
			return $this->json(['data' => $res, 'token' => $token]);
		}
	}

	//验证登录
	private function checkLogin($data)
	{
		$where['username'] = $data['username'];
		$info =	Users::where($where)->find();

		if ($info) {
			$info = $info->toArray();
			$UsersRoles = UsersRoles::getinfo($info);
			$info['roles_title'] = $UsersRoles['title'];
			$info['roles_status'] = $UsersRoles['status'];
			$info['is_console'] = $UsersRoles['is_console'];

			if ($info['password'] === pass_hash($data['password'], $info["salt"])) {
				unset($info['password']);
				unset($info["salt"]);
				if (!($info['status']) || !($info['roles_status'])) {
					throw new ValidateException("该账户被禁用");
				}
			} else {
				throw new ValidateException("用户名或者密码不正确");
			}
			if (empty($info['lastweid']) && !empty($info['weid'])) {
				$info['lastweid'] = $info['weid'];
				Users::where('id', $info['id'])->update(['lastweid' => $info['weid']]);
			}
		}
		Users::where('id', $info['id'])->update(['login_time' => time(), 'login_ip' => client_ip()]);
		event('LoginLog', ['user' => $data['username'], 'weid' => $info['weid']]);	//写入登录日志

		return $info;
	}

	function sitesetup()
	{
		$id = (int) input('post.i', '', 'intval');
		$ac = [];
		$res = Config::getsitesetupconfig('sitesetup');
		if (empty($res)) {
			$res = [];
		}
		if (!empty($id)) {
			$ac	= Platform::field('title,logo,loginbgimg')->find($id);
			if ($ac) {
				$ac = $ac->toArray();
			}
		}
		if (empty($ac)) {
			$ac['title'] = $res['sys_title'];
			$ac['logo'] = $res['logo'];
		}
		$res['ac'] = $ac;
		return $this->json(['data' => $res]);
	}
}
