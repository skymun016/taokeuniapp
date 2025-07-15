<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use think\facade\Validate;
use app\model\Technical;
use app\model\Agent;
use app\model\Partner;
use app\model\Tuanzhang;
use app\model\Member;
use app\model\Store;
use app\model\Operatingcity;
use app\model\RegisterField;
use app\model\Category;
use app\model\TechnicalCertificate;
use app\model\Users;
use app\model\Openid;

class DiyfieldsController extends Base
{
	function getModel()
	{
		$ptype = $this->getPath()[1];
		if ($ptype == 'technical') {
			return new Technical;
		} else if ($ptype == 'agent') {
			return new Agent;
		} else if ($ptype == 'member') {
			return new Member;
		} else if ($ptype == 'operatingcity') {
			return new Operatingcity;
		} else if ($ptype == 'store') {
			return new Store;
		} else if ($ptype == 'partner') {
			return new Partner;
		} else if ($ptype == 'tuanzhang') {
			return new Tuanzhang;
		}
	}
	function getPath()
	{
		$path = input('post.path', '', 'serach_in');
		if (!empty($path)) {
			$tmppath = explode('/', $path);
		}
		if (empty($tmppath)) {
			$con = input('con');
			if (!empty($con)) {
				$tmppath[1] = $con;
			}
		}

		return $tmppath;
	}

	public function update()
	{
		$id = $this->request->post('id');
		$uuid = $this->request->post('uuid');
		$postdata = only('id,fields');

		$regdata = RegisterField::fieldToData($postdata, 'pc');

		$data = $regdata['data'];
		unset($data['create_time']);

		if ($data['username'] && empty($id)) {
			$data['uuid'] = uniqid(rand(1, 10000));
		}
		$usersdata['password'] = $data['password'];

		$data['password'] = '12345678';

		$validate = Validate::rule($regdata['rule']);
		if (!$validate->check($data)) {
			throw new ValidateException($validate->getError());
		}

		unset($data['password']);
		if (empty($id)) {
			$usersdata['username'] = trim($data['username']);
		}
		unset($data['username']);

		if ($usersdata['username']) {
			if (Users::where('username', $usersdata['username'])->find()) {
				throw new ValidateException('用户名已被占用');
			}
		}

		if ($this->getPath()[1] == 'technical') {
			if ($data['photoalbum']) {
				$data['touxiang'] = explode(',', $data['photoalbum'])[0];
			}
		}

		if (empty($id)) {
			$data['uid'] = 0;
			$data['weid'] = weid();
			$data['sort'] = 100;
			try {
				$res = $this->getModel()->create($data);
				if ($this->getPath()[1] == 'store' || $this->getPath()[1] == 'technical' || $this->getPath()[1] == 'tuanzhang' || $this->getPath()[1] == 'operatingcity') {

					if ($res->id && !empty($usersdata['username'])) {
						if (!empty($usersdata['password'])) {
							$usersdata["salt"] = substr(md5(uniqid()), 8, 8);
							$usersdata['password'] = pass_hash($usersdata['password'], $usersdata["salt"]);
						} else {
							unset($usersdata['password']);
						}
						if ($this->getPath()[1] == 'store') {
							$usersdata['sid'] = $res->id;
						}
						if ($this->getPath()[1] == 'operatingcity') {
							$usersdata['ocid'] = $res->id;
						}
						if ($this->getPath()[1] == 'tuanzhang') {
							$usersdata['tzid'] = $res->id;
						}

						if ($this->getPath()[1] == 'technical') {
							$usersdata['tid'] = $res->id;
						}

						$usersdata['weid'] = weid();
						$usersdata['uuid'] = $data['uuid'];
						$usersdata['w7uid'] = 0;
						Users::create($usersdata);
					}
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			$data['id'] = $id;

			if ($this->getPath()[1] == 'technical') {
				$Technicalwhere['id'] = $data['id'];
				$Technicalwhere['province_name'] = $data['province_name'];
				$Technicalwhere['city_name'] = $data['city_name'];
				$Technicalwhere['district_name'] = $data['district_name'];
				$Technicalwhere['house_number'] = $data['house_number'];
				if (Technical::where($Technicalwhere)->find()) {
					unset($data['latitude']);
					unset($data['longitude']);
					unset($data['region_name']);
				}
			}
			if ($this->getPath()[1] == 'store') {
				$storewhere['id'] = $data['id'];
				$storewhere['province_name'] = $data['province_name'];
				$storewhere['city_name'] = $data['city_name'];
				$storewhere['district_name'] = $data['district_name'];
				$storewhere['house_number'] = $data['house_number'];
				if (Store::where($storewhere)->find()) {
					unset($data['latitude']);
					unset($data['longitude']);
					unset($data['region_name']);
				}
			}
			try {
				if (!empty($usersdata['password'])) {
					$usersdata["salt"] = substr(md5(uniqid()), 8, 8);
					$usersdata['password'] = pass_hash($usersdata['password'], $usersdata["salt"]);

					Users::where('uuid', $uuid)->update($usersdata);
				} else {
					unset($usersdata['password']);
				}

				$this->getModel()->update($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '操作成功']);
		}
	}
	public function audit()
	{
		$id = $this->request->post('id');
		$data['id'] = $id;
		unset($data['create_time']);
		$data['status'] = 1;

		try {
			$this->getModel()->update($data);
		} catch (\Exception $e) {
			throw new ValidateException($e->getMessage());
		}
		return $this->json(['msg' => '审核成功']);
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		$path = input('post.path', '', 'serach_in');
		$is_tzstore =  $this->request->post('is_tzstore', '', 'serach_in');
		if ($is_tzstore == 1) {
			$id = Store::getidbytzid($id);
		}

		$ptype = $this->getPath()[1];
		$data = RegisterField::getinputField($ptype);

		if (!empty($id)) {

			$infodata = $this->getModel()->find($id);

			if (!empty($infodata)) {
				$infodata = $infodata->toArray();
				$infodata['password'] = "";

				if (!empty($infodata['uuid'])) {

					$infodata['user'] = Users::getuserbyuuid($infodata['uuid']);
					unset($infodata['user']['password']);
					unset($infodata['user']['salt']);
					$infodata['username'] = $infodata['user']['username'];

					if (!empty($infodata['user']['id'])) {
						$infodata['Openid'] = Openid::getMpOpenidbyuid($infodata['user']['id']);
					}
					if (empty($infodata['Openid'])) {
						$infodata['Openid'] = Openid::getMpOpenidbyuuid($infodata['uuid']);
					}
				}

				$area[0] = areaconversion($infodata['province_name']);
				$area[1] = $infodata['city_name'];
				if (!empty($infodata['district_name'])) {
					$area[2] = $infodata['district_name'];
				}

				$infodata['region_name'] = $area;

				$customtext = iunserializer($infodata['customtext']);
			}
		}

		foreach ($data as &$vo) {

			if ($vo['is_sys'] == 1) {
				if (!empty($infodata[$vo['fieldsmingcheng']])) {
					$vo['fieldsvalue'] = $infodata[$vo['fieldsmingcheng']];
				}
				if ($vo['fieldsmingcheng'] == "sex") {
					$vo['selectvaluearray'] = sexarray();
				}

				if ($vo['fieldsmingcheng'] == 'category_id') {
					$vo['selectvaluearray'] = \app\model\TechnicalCertificate::getpcarray();
				}

				if ($vo['fieldsmingcheng'] == 'sid') {
					$vo['selectvaluearray'] = Store::getpcarray();
				}

				if ($vo['fieldsmingcheng'] == 'level') {
					if ($vo['ptype'] == 'technical') {
						$vo['selectvaluearray'] = \app\model\TechnicalLevel::getpcarray();
					} elseif ($vo['ptype'] == 'tuanzhang') {
						$vo['selectvaluearray'] = \app\model\TuanzhangLevel::getpcarray();
					} elseif ($vo['ptype'] == 'partner') {
						$vo['selectvaluearray'] = \app\model\PartnerLevel::getpcarray();
					} elseif ($vo['ptype'] == 'operatingcity') {
						$vo['selectvaluearray'] = \app\model\OperatingcityLevel::getpcarray();
					}  elseif ($vo['ptype'] == 'store') {
						$vo['selectvaluearray'] = \app\model\StoreLevel::getpcarray();
					}
				}
				if ($vo['fieldsmingcheng'] == 'agent_level') {
					if ($vo['ptype'] == 'agent') {
						$vo['selectvaluearray'] = \app\model\AgentLevel::getpcarray();
					}
				}

				if ($vo['fieldsmingcheng'] == 'areatype') {
					$vo['selectvaluearray'] = \app\model\OperatingcityType::getpcarray();
				}

				if ($vo['fieldsmingcheng'] == 'gid') {
					$vo['selectvaluearray'] = \app\model\MemberAuthGroup::getpcarray();
				}

				if ($vo['inputtype'] == 'checkbox' && $vo['fieldsmingcheng'] == 'cate_ids') {
					if (empty($vo['fieldsvalue'])) {
						$vo['fieldsvalue'] = [];
					} else {
						$vo['fieldsvalue'] =  explode(',', $vo['fieldsvalue']);
					}
					if ($vo['ptype'] == 'technical') {
						$ctopptype = 2;
					} else {
						$ctopptype = '';
					}
					$vo['selectvaluearray'] = Category::gettoparray($ctopptype);
				}
				if ($vo['inputtype'] == 'checkbox' && $vo['fieldsmingcheng'] == 'certificate_ids') {
					if (empty($vo['fieldsvalue'])) {
						$vo['fieldsvalue'] = [];
					} else {
						$vo['fieldsvalue'] =  explode(',', $vo['fieldsvalue']);
					}
					$vo['selectvaluearray'] = TechnicalCertificate::getpcarray($ctopptype);
				}
			} else {
				$vo['fieldsvalue'] = $customtext[$vo['inputtype']][$vo['id']];
				if (!empty($vo['selectvalue'])) {
					$selectvaluearray = explode(',', $vo['selectvalue']);
					if (is_array($selectvaluearray)) {
						foreach ($selectvaluearray as $key => $svo) {
							$vo['selectvaluearray'][$key]['val'] = $svo;
							$vo['selectvaluearray'][$key]['key'] = $svo;
						}
					}
				}
			}

			if ($vo['inputtype'] == 'pics') {
				$vo['fieldsvalue1'] = $vo['fieldsvalue'];
				if (empty($vo['fieldsvalue'])) {
					$vo['fieldsvalue'] = [];
				} else {
					$vo['fieldsvalue'] = setPicsView($vo['fieldsvalue']);
				}
			}

			if ($vo['inputtype'] == 'date') {
				$vo['fieldsvalue'] =  time_format($vo['fieldsvalue']);
			}
		}
		if (!empty($infodata)) {
			$retdata['id'] = $infodata['id'];
			$retdata['uuid'] = $infodata['uuid'];
			$retdata['infodata'] = $infodata;
		}

		$retdata['fields'] = $data;
		$retdata['path'] = $path;

		return $this->json(['data' => $retdata]);
	}

	function delete()
	{
		return $this->del($this->getModel());
	}
	/*
 	*  导入
 	*/
	public function importData()
	{
		$data = input('post.');
		$ptype = $this->getPath()[1];
		$mob = $this->getModel();
		RegisterField::importData($mob, $ptype, $data);
		return $this->json(['msg' => '操作成功']);
	}

	public function get_idbytitle($list, $title)
	{
		foreach ($list as  $v) {
			if ($v['key'] == $title) {
				return $v['val'];
			}
		}
		return 0;
	}
	public function get_idbyarea_name($list, $area_name)
	{
		foreach ($list as  $v) {
			if ($v['area_name'] == $area_name) {
				return $v['area_id'];
			}
		}
		return 0;
	}
}
