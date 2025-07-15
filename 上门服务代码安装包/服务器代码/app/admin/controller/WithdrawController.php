<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Withdraw;
use app\model\Message;
use app\model\Agent;
use app\model\Technical;
use app\model\Store;
use app\model\Tuanzhang;
use app\model\MemberBankcard;

class WithdrawController extends Base
{

	function getPath()
	{
		$path = input('post.path', '', 'serach_in');
		if (!empty($path)) {
			$tmppath = explode('/', $path);
		}

		return $tmppath;
	}
	function index()
	{
		$weid = weid();
		$path = input('post.path', '', 'serach_in');
		//$mo = input('post.mo', '', 'serach_in');
		$page = input('post.page', 1, 'intval');
		$keyword = input('post.keyword', '', 'serach_in');

		$query = Withdraw::where(['weid' => $weid]);

		$mo = $this->getPath()[2];

		if (!empty($mo)) {
			$query->where('mo', $mo);
		}

		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}
		if (!empty($this->ocid)) {
			$query->where('ocid', $this->ocid);
		}
		if (!empty($mo)) {
			$query->where('mo', $mo);
		}

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['sid'] = Store::getTitle($vo['sid']);
			$vo['MemberBankcard'] = MemberBankcard::getbankcard($vo['bid']);
			if ($vo['mo'] == 'agent') {
				$vo['username'] = Agent::getTitle($vo['uid']);
			} elseif ($vo['mo'] == 'technical') {
				$vo['username'] = Technical::getTitlebyuid($vo['uid']);
			} elseif ($vo['mo'] == 'store') {
				$vo['username'] = Store::getTitlebyuid($vo['uid']);
			} elseif ($vo['mo'] == 'tuanzhang') {
				$vo['username'] = Tuanzhang::getTitlebyuid($vo['uid']);
			}
		}

		$data['data'] = $res;

		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');

		if ($data['status'] == 1) {
			$res = Withdraw::audit($data['id']);
			if (!empty($res['code'])) {
				return $this->json(['msg' => '审核成功']);
			} else {
				throw new ValidateException($res['return_msg'] . $res['err_code_des']);
			}
		}

		Withdraw::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');

		$data = Withdraw::find($id);
		if (!empty($data)) {
			$data = $data->toArray();
			if ($data['mo'] == 'agent') {
				$data['username'] = Agent::getTitle($data['uid']);
			} elseif ($data['mo'] == 'technical') {
				$data['username'] = Technical::getTitle($data['uid']);
			} elseif ($data['mo'] == 'store') {
				$data['username'] = Store::getTitlebyuid($data['uid']);
			} elseif ($data['mo'] == 'tuanzhang') {
				$data['username'] = Tuanzhang::getTitlebyuid($data['uid']);
			}

			$data['MemberBankcard'] = MemberBankcard::getbankcard($data['bid']);
		}

		return $this->json(['data' => $data]);
	}

	public function audit()
	{
		$id = $this->request->post('id');

		$res = Withdraw::audit($id);
		if (!empty($res['code'])) {
			return $this->json(['msg' => '审核成功']);
		} else {
			return $this->json(['msg' => $res['return_msg'] . $res['err_code_des']]);
		}
	}

	function delete()
	{
		return $this->del(new Withdraw());
	}
}
