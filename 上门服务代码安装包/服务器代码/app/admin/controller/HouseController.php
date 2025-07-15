<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\House;
use app\model\HouseDescription;
use app\model\HouseImage;
use app\model\Store;
use app\model\Operatingcity;
use app\model\Tuanzhang;

class HouseController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$path = input('post.path', '', 'serach_in');
		$ptype = input('post.ptype', '', 'serach_in');
		$status = input('post.status', '', 'serach_in');

		$keyword = input('post.keyword', '', 'serach_in');
		$cat_id = input('post.cat_id', '', 'serach_in');

		$query = House::where(['weid' => $weid]);

		if ($path == '/housesell/index') {
			$ptype = 1;
		} else if ($path == '/househire/index') {
			$ptype = 2;
		}

		if (!empty($ptype)) {
			$query->where('ptype', $ptype);
		} else {
			$query->where('ptype', '>', 0);
		}

		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		if (!empty($this->tzid)) {
			$query->where('sid', Store::getidbytzid($this->tzid));
		}

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
					$query->where('province_name', 'like', '%' . $Operatingcitydata['province_name'] . '%');
				}
			}
		}

		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		if (!empty($cat_id)) {
			$query->where('cat_id',  $cat_id);
		}

		if (!empty($status) || $status === "0") {
			$query->where(['status' => $status]);
		}

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		$sql = $query->getLastsql();

		foreach ($res['data'] as &$vo) {
			$vo['image'] = toimg($vo['image']);


			if ($vo['sid'] == 0) {
				$vo['name'] = '[自营]' . $vo['name'];
			} else {
				$StoreTitle = Store::getTitle($vo['sid']);
				if (empty($StoreTitle)) {
					$vo['name'] = '[商户已被删除]' . $vo['name'];
				} else {
					$vo['name'] = '[' . $StoreTitle . ']' . $vo['name'];
				}
			}
			$vo['hallroom'] = $vo['room'] . '室' . $vo['hall'] . '厅' . $vo['toilet'] . '卫';
		}
		$data['data'] = $res;
		if ($page == 1) {
			//$data['field_data']['cidarray'] = _generateSelectTree(Category::getpcarray());
		}
		$data['sql'] = $sql;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		House::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = request()->post('id');
		$data = input('post.');
		unset($data['create_time']);
		$data['image'] = $data['images'][0]['url'];
		if (!empty($data['keyword'])) {
			$data['keyword'] = implode(',', $data['keyword']);
		}

		if (!empty($data['videourl'])) {
			$data['videotype'] = 1;
		} elseif (!empty($data['videoid'])) {
			$data['videotype'] = 2;
		} else {
			$data['videotype'] = 0;
		}

		if (empty($id)) {
			$data['weid'] = weid();
			if (!empty($this->sid)) {
				$data['sid'] = $this->sid;

				$storemod = Store::find($this->sid);
				if ($storemod) {
					$data['province_name'] = $storemod->province_name;
					$data['city_name'] = $storemod->city_name;
					$data['district_name'] = $storemod->district_name;
				}
			}

			if (!empty($this->tzid)) {
				$data['sid'] = Store::getidbytzid($this->tzid);
				$Tuanzhangmod = Tuanzhang::find($this->tzid);
				if ($Tuanzhangmod) {
					$data['province_name'] = $Tuanzhangmod->province_name;
					$data['city_name'] = $Tuanzhangmod->city_name;
					$data['district_name'] = $Tuanzhangmod->district_name;
				}
			}

			if (!empty($this->ocid)) {
				$data['ocid'] = $this->ocid;
				if (!empty($this->ocid)) {
					$ocmod = Operatingcity::find($this->ocid);
					if ($ocmod) {
						$data['province_name'] = $ocmod->province_name;
						$data['city_name'] = $ocmod->city_name;
						$data['district_name'] = $ocmod->district_name;
					}
				}
			}

			if (empty($data['tel'])) {
				$data['tel'] = '';
			}
			try {
				$res = House::create($data);
				if ($res->id && empty($data['sort'])) {
					House::update(['sort' => $res->id, 'id' => $res->id]);
				}
				$data['id'] = $res->id;
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				House::update($data);
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}

	function _synupdata($data)
	{
		//详情
		if (empty(HouseDescription::where('house_id', $data['id'])->find())) {
			HouseDescription::create([
				'house_id' => (int) $data['id'],
				'description' => $data['description']
			]);
		} else {
			HouseDescription::where('house_id', $data['id'])->update(['description' => $data['description']]);
		}

		//图片
		HouseImage::where('house_id', $data['id'])->delete();
		if (isset($data['images'])) {
			foreach ($data['images'] as $image) {
				HouseImage::create([
					'house_id' => (int) $data['id'],
					'weid' => weid(),
					'image' => $image['url']
				]);
			}
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		//if (!$id) throw new ValidateException('参数错误');
		if ($id) {
			$data = House::field('*')->find($id)->toArray();
		} else {
			$data = [
				'sort' => 100,
				'status' => 1,
			];
		}
		if (!empty($data['keyword'])) {
			$data['keyword'] = explode(',', $data['keyword']);
		} else {
			$data['keyword'] = [];
		}

		$GD = HouseDescription::where(['house_id' => $id])->find();

		if (!empty($GD)) {
			$data['description'] = \app\model\DomainReplace::setreplace($GD->description);
		}

		$goods_image = HouseImage::where(['house_id' => $id])
			->field('image')
			->order('id asc')
			->select()->toArray();

		if (!empty($goods_image)) {
			foreach ($goods_image as $key => $vo) {
				$data['images'][$key]['url'] = toimg($vo['image']);
			}
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new House());
	}
	function getField()
	{
		$ptype = input('post.ptype', '', 'serach_in');

		return $this->json(['data' => $data]);
	}
}
