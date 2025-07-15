<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Partner;
use app\model\RegisterField;

class PartnerController extends Base
{
	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$ptype = 'partner';

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
				$vo = Partner::conversion($vo);
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
		$path = input('post.path', '', 'serach_in');
		$weid = weid();

		if ($path == "/partner/audit") {
			$status = "0";
		}

		$query = Partner::where(['weid' => $weid]);

		if (!empty($keyword)) {
			$query->where('title|tel', 'like', '%' . $keyword . '%');
		}

		if (!empty($status) || $status === "0") {
			$query->where(['status' => $status]);
		}
		return $query;
	}
	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Partner::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	function delete()
	{
		return $this->del(new Partner());
	}
}
