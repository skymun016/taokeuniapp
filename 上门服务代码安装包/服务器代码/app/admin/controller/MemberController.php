<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Member;
use app\model\MemberAuthGroup;
use app\model\RegisterField;
use app\model\Cashregister;
use app\model\Users;

class MemberController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$ptype = 'member';

		$Fielddata = RegisterField::where(['weid' => $weid, 'ptype' => $ptype])->select()->toArray();

		if (empty($Fielddata)) {
			RegisterField::datainitial($ptype);
		}

		$query = $this->setSearch();

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		if (!empty($res['data'])) {
			foreach ($res['data'] as &$vo) {
				$vo = Member::conversion($vo);
			}
		}
		$data['data'] = $res;
		if ($page == 1) {
			$data['field_data']['RegisterField'] = RegisterField::getlistViewField($ptype);
		}

		return $this->json($data);
	}
	function setSearch()
	{
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = input('post.status', '', 'serach_in');
		$regdate = input('post.regdate', '', 'serach_in');
		$path = input('post.path', '', 'serach_in');
		$weid = weid();

		if ($path == "/member/audit") {
			$status = "0";
		}

		$query = Member::where(['weid' => $weid]);

		if (!empty($regdate)) {
			$query->where('regdate', 'between', [strtotime($regdate[0]), strtotime($regdate[1])]);
		}

		if (!empty($keyword)) {

			$query->where('nickname|telephone', 'like', '%' . $keyword . '%');
		}

		if (!empty($status) || $status === "0") {
			$query->where(['status' => $status]);
		}
		return $query;
	}
	function reg()
	{
		$postdata = input('post.');
		$weid = weid();

		if (empty($postdata['telephone'])) {
			throw new ValidateException('手机号不能为空');
		}

		$chackMember = Member::where(['weid' => $weid, 'telephone' => $postdata['telephone']])->find();
		if ($chackMember) {
			throw new ValidateException('手机号已被占用');
		}
		$membergroup = MemberAuthGroup::getdefaultGroup();;
		$gid = $membergroup['id'];

		$member['sex'] = (int) $postdata['sex'];
		$member['weid'] = $weid;
		$member['reg_type'] = 'cashregister';
		$member['gid'] = $gid;
		$member['regdate'] = time();
		$member['lastdate'] = time();
		$member['nickname'] = $postdata['nickname'];
		$member['telephone'] = $postdata['telephone'];

		if (empty($member['nickname'])) {
			$member['nickname'] = '收银台会员';
		}

		$res = Member::create($member);

		if ($res->id && $res->nickname == '收银台会员') {
			Member::update(['nickname' => $res->nickname . '_' . $res->id, 'id' => $res->id]);
		}
		if ($res->id) {
			$cash = Cashregister::getcash(['operator_id' => (int) $this->userInfo['id']]);

			$res = Cashregister::where([
				'id' => $cash['id']
			])->update(['uid' => (int) $res->id]);
		}

		return $this->json(['membe' => $res, 'cash' => $cash]);
	}
	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Member::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	function delete()
	{
		return $this->del(new Member());
	}
	//导出
	function dumpdata()
	{
		$page = $this->request->post('page', 1, 'intval');
		$ptype = 'member';
		$query = $this->setSearch();
		$data = RegisterField::dumpdata($query, $ptype, $page);
		return $this->json($data);
	}
}
