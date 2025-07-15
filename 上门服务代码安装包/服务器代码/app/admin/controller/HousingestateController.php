<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\HousingEstate;
use app\model\Operatingcity;
use app\model\Tuanzhang;
use app\model\Geocoder;

class HousingestateController extends Base
{

	function index()
	{
		$query = $this->setSearch();
		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		if (!empty($res['data'])) {
			foreach ($res['data'] as &$vo) {
				$vo['region_name'] = $vo['province_name'] . $vo['city_name'] . $vo['district_name'];
				$vo['tuanzhang'] = Tuanzhang::getInfobyid($vo['tzid']);
			}
		}
		$data['data'] = $res;

		return $this->json($data);
	}
	function my()
	{
		$query = $this->setSearch();
		$query->where(['tzid' => (int) $this->tzid]);
		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		if (!empty($res['data'])) {
			foreach ($res['data'] as &$vo) {
				$vo['region_name'] = $vo['province_name'] . $vo['city_name'] . $vo['district_name'];
				$vo['tuanzhang'] = Tuanzhang::getInfobyid($vo['tzid']);
			}
		}
		$data['data'] = $res;

		return $this->json($data);
	}

	function setSearch()
	{
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = input('post.status', '', 'serach_in');
		$path = input('post.path', '', 'serach_in');
		$weid = weid();

		if ($path == "/housingestate/audit") {
			$status = "0";
		}

		$query = HousingEstate::where(['weid' => $weid]);

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
		HousingEstate::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		if (empty($data['tzid'])) {
			throw new ValidateException('请选择所属社区');
		}
		$areaarray = $data['region_name'];
		$data['province_name'] = $areaarray[0];
		$data['city_name'] = $areaarray[1];
		$data['district_name'] = $areaarray[2];
		
		$dizhi = $data['province_name'];
		if ($data['city_name']) {
			$dizhi = $dizhi . $data['city_name'];
		}
		if ($data['district_name']) {
			$dizhi = $dizhi . $data['district_name'];
		}
		$data['area_name'] = $dizhi;
		if ($data['house_number']) {
			$dizhi = $dizhi . $data['house_number'];
		}
		
		if ($dizhi) {
			$coder = Geocoder::geocoding($dizhi);
			$data['latitude'] = $coder['latitude'];
			$data['longitude'] = $coder['longitude'];
			$data['region_name'] = $dizhi;
		}

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = HousingEstate::create($data);
				if ($res->id && empty($data['sort'])) {
					HousingEstate::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				HousingEstate::update($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}
	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$data = HousingEstate::find($id);

		if ($data) {
			$data = $data->toArray();
			$data['pic'] = toimg($data['pic']);

			$area[0] = areaconversion($data['province_name']);
			$area[1] = $data['city_name'];
			if (!empty($data['district_name'])) {
				$area[2] = $data['district_name'];
			}
			$data['region_name'] = $area;
		}

		return $this->json(['data' => $data]);
	}
	function delete()
	{
		return $this->del(new HousingEstate());
	}
	function getField()
	{
		$weid = weid();
		$query = Tuanzhang::where(['weid' => $weid, 'status' => 1]);

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
		$data = $query->order('sort asc')->select()->toArray();
		foreach ($data as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['title'];
		}
		$data['tzidarray'] = $array;
		return $this->json(['data' => $data]);
	}
}
