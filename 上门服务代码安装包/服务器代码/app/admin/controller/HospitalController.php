<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Hospital;
use app\model\HospitalCate;
use app\model\Operatingcity;

class HospitalController extends Base
{

	function index()
	{
		$query = $this->setSearch();

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		if (!empty($res['data'])) {
			foreach ($res['data'] as &$vo) {
				$vo = Hospital::conversion($vo);
			}
		}
		$data['data'] = $res;

		return $this->json($data);
	}
	function setSearch()
	{
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = input('post.status', '', 'serach_in');
		$weid = weid();

		$query = Hospital::where(['weid' => $weid]);

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
		Hospital::update($data);

		return $this->json(['msg' => '操作成功']);
	}
	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		if (empty($id)) {
			$data['weid'] = weid();

			try {
				$res = Hospital::create($data);
				if ($res->id && empty($data['sort'])) {
					Hospital::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				Hospital::update($data);
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
		$data = Hospital::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}
	function delete()
	{
		return $this->del(new Hospital());
	}
}
