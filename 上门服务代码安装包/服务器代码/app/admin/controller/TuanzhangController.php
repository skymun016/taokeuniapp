<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Tuanzhang;
use app\model\TuanzhangLevel;
use app\model\Category;
use app\model\RegisterField;
use app\model\Operatingcity;
use app\model\Users;

class TuanzhangController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$ptype = 'tuanzhang';

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
				$vo = Tuanzhang::conversion($vo);
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

		if ($path == "/tuanzhang/audit") {
			$status = "0";
		}

		$query = Tuanzhang::where(['weid' => $weid]);

		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}
		if (!empty($this->ocid)) {
			$Operatingcitydata = Operatingcity::find($this->ocid);
			if ($Operatingcitydata) {
				$Operatingcitydata = $Operatingcitydata->toArray();
				if(empty($Operatingcitydata['areatype'])){
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
		Tuanzhang::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	function delete()
	{
		
		return $this->del(new Tuanzhang());
	}
	 //导出
	 function dumpdata()
	 {
		 $page = $this->request->post('page', 1, 'intval');
		 $ptype = 'tuanzhang';
		 $query = $this->setSearch();
		 $data = RegisterField::dumpdata($query, $ptype, $page);
		 return $this->json($data);
	 }
}
