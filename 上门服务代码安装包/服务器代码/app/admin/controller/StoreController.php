<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Store;
use app\model\StoreLevel;
use app\model\Category;
use app\model\RegisterField;
use app\model\Operatingcity;
use app\model\Tuanzhang;
use app\model\Users;

class StoreController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$ptype = 'store';

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
				$vo = Store::conversion($vo);
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

		if ($path == "/store/audit") {
			$status = "0";
		}

		$query = Store::where(['weid' => $weid, 'tzid' => 0]);

		if (!empty($this->ocid)) {
			$Operatingcitydata = Operatingcity::find($this->ocid);
			if ($Operatingcitydata) {
				$Operatingcitydata = $Operatingcitydata->toArray();
				if (empty($Operatingcitydata['areatype'])) {
					$Operatingcitydata['areatype'] = 3;
				}

				if ($Operatingcitydata['areatype'] == 3) {
					$query->where('district_name', $Operatingcitydata['district_name']);
				} elseif ($Operatingcitydata['areatype'] == 2) {
					$query->where('city_name', $Operatingcitydata['city_name']);
				} elseif ($Operatingcitydata['areatype'] == 1) {
					$query->where('province_name', $Operatingcitydata['province_name']);
				}
			}
		}
		if (!empty($this->tzid)) {

			$Tuanzhangdata = Tuanzhang::find($this->tzid);

			if ($Tuanzhangdata) {
				$Tuanzhangdata = $Tuanzhangdata->toArray();
				$query->where('district_name', $Tuanzhangdata['district_name']);
			} else {
				$query->where('district_name', '无');
			}
		}

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
		Store::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	function delete()
	{
		return $this->del(new Store());
	}
	//导出
	function dumpdata()
	{
		$page = $this->request->post('page', 1, 'intval');
		$ptype = 'store';
		$query = $this->setSearch();
		$data = RegisterField::dumpdata($query, $ptype, $page);
		return $this->json($data);
	}
}
