<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Operatingcity;
use app\model\OperatingcityLevel;
use app\model\OperatingcityType;
use app\model\Category;
use app\model\Store;
use app\model\RegisterField;

use app\model\Users;

class OperatingcityController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$ptype = 'operatingcity';

		$Fielddata = RegisterField::where(['weid' => $weid, 'ptype' => $ptype])->select()->toArray();

		if (empty($Fielddata)) {
			RegisterField::datainitial($ptype);
		}

		$query = $this->setSearch();

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		if (!empty($res['data'])) {
			foreach ($res['data'] as &$vo) {
				$vo = Operatingcity::conversion($vo);
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

		if ($path == "/operatingcity/audit") {
			$status = "0";
		}

		$query = Operatingcity::where(['weid' => $weid]);

		if (!empty($this->ocid)) {
			$query->where('id', '<>',$this->ocid);
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
					$query->where('province_name', 'like', '%' . $Operatingcitydata['province_name'] . '%' );
				}
			}
		}

		if (!empty($keyword)) {

			$query->where('title|region_name|province_name|city_name|district_name', 'like', '%' . $keyword . '%');
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
		Operatingcity::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	function delete()
	{
		return $this->del(new Operatingcity());
	}

	function getcityconfigInfo()
	{
		if (!empty($this->ocid)) {
			$res = Operatingcity::getsettings($this->ocid);
			if(empty($res['city_discount_method'])){
				$res['city_discount_method'] = 0;
			}
			return $this->json(['data' => $res]);
		}
	}

	public function cityconfigupdate()
	{
		if (!empty($this->ocid)) {
			$config = $this->request->post();
			$configstr = serialize($config);
			Operatingcity::update(['settings' => $configstr], ['id' => $this->ocid]);

			return $this->json(['msg' => '操作成功']);
		}
	}
}
