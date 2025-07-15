<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Address;
use app\model\Area;
use app\model\Member;
use app\model\MemberAuthGroup;
use app\model\Geocoder;

class AddressController extends Base
{

    public function add()
    {

        $data = array_filter($this->getdata());
        $data['weid'] = weid();

        if (empty($data['name'])) {
            throw new ValidateException('请输入姓名');
        }

        if (empty($data['telephone'])) {
            throw new ValidateException('请输入电话号码');
        }

        if (empty($data['region_name'])) {
            throw new ValidateException('请选定位');
        }

        if (empty($data['address'])) {
            throw new ValidateException('请输入地址');
        }

        try {
            $res = Address::create($data);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }
        return $this->json(['msg' => '添加成功', 'data' => $res->id]);
    }

    public function update()
    {
        $data = $this->getdata();
        $id = input('post.id', '', 'intval');

        $data['province_id'] = (int) $data['province_id'];
        $data['city_id'] = (int) $data['city_id'];
        $data['district_id'] = (int) $data['district_id'];
        $data['street'] = (int) $data['street'];

        unset($data['weid']);
        unset($data['uid']);
        $data['id'] = $id;
        if (!empty($id)) {
            try {
                Address::update($data);
            } catch (\Exception $e) {
                throw new ValidateException($e->getMessage());
            }
        }
        return $this->json(['data' => $data]);
    }

    public function delete()
    {
        $idx =  $this->request->post('id', '', 'serach_in');
        if (!$idx) throw new ValidateException('参数错误');
        Address::destroy(['id' => $idx], true);
        return $this->json(['msg' => '操作成功']);
    }

    public function getdata()
    {
        $postdata['weid'] = weid();
        $postdata['uid'] = UID();
        $postdata['is_bindingaddress'] = (int) input('post.is_bindingaddress', '', 'intval');
        $postdata['name'] = input('post.name', '', 'serach_in');
        $postdata['telephone'] = input('post.telephone', '', 'serach_in');
        $postdata['address'] = input('post.address', '', 'serach_in');
        $postdata['region_name'] = input('post.region_name', '', 'serach_in');
        $postdata['province_name'] = input('post.province_name', '', 'serach_in');
        $postdata['city_name'] = input('post.city_name', '', 'serach_in');
        $postdata['district_name'] = input('post.district_name', '', 'serach_in');
        $postdata['latitude'] = input('post.latitude', '', 'serach_in');
        $postdata['longitude'] = input('post.longitude', '', 'serach_in');

        if (input('post.street', '', 'serach_in')) {
            $postdata['street'] = input('post.street', '', 'serach_in');
        }

        if (input('post.city_id', '', 'serach_in')) {
            $postdata['city_id'] = input('post.city_id', '', 'serach_in');
        }
        if (input('post.district_id', '', 'serach_in')) {
            $postdata['district_id'] = input('post.district_id', '', 'serach_in');
        }
        if (input('post.province_id', '', 'serach_in')) {
            $postdata['province_id'] = input('post.province_id', '', 'serach_in');
        }

        $postdata['isDefault'] = input('post.isDefault', '', 'serach_in');

        return $postdata;
    }

    public function setdefault()
    {

        $id = input('post.id', '', 'intval');
        $postdata['isDefault'] = 1;
        $postdata['id'] = $id;

        if (!empty($id)) {
            Address::where(['weid' => weid(), 'uid' => UID()])->update(['isDefault' => 0]);
            Address::update($postdata);
        }
        $data['id'] = $id;

        return $this->json(['data' => $data]);
    }

    public function detail()
    {
        $id = input('get.id', '', 'intval');
        if (!empty($id)) {
            $where['weid'] = weid();
            $where['uid'] = UID();
            $where['id'] = $id;
            $data = Address::where($where)->find();
            if (!empty($data)) {
                $data = $data->toArray();
            }
            $Area = new Area;
            $data['provinceStr'] = $Area->get_area_name($data['province_id']);
            $data['cityStr'] = $Area->get_area_name($data['city_id']);
            $data['areaStr'] = $Area->get_area_name($data['district_id']);
        }
        return $this->json(['data' => $data]);
    }

    public function default()
    {
        $uid = UID();
        $where['weid'] = weid();
        $where['uid'] = $uid;
        $query = Address::where($where);

        $Member = Member::find($uid);
        if (!empty($Member)) {
            $Member = $Member->toArray();
            $MemberAuthGroup = MemberAuthGroup::find($Member['gid']);
            if (!empty($MemberAuthGroup)) {
                $MemberAuthGroup = $MemberAuthGroup->toArray();
            }
        }
        if ($MemberAuthGroup['is_bindingaddress'] == 1) {
            $query->where('is_bindingaddress', 1);
        }

        $query->order('isDefault desc');

        $data = $query->find();

        if (!empty($data)) {
            $data = $data->toArray();
        }

        if (empty($data['province_name'])) {
            $province_name = Area::get_area_name($data['province_id']);
            if (!empty($province_name)) {
                $data['province_name'] = $province_name;
            }
        }

        if (empty($data['city_name'])) {
            $city_name = Area::get_area_name($data['city_id']);
            if (!empty($city_name)) {
                $data['city_name'] = $city_name;
            }
        }

        if (empty($data['district_name'])) {
            $district_name = Area::get_area_name($data['district_id']);
            if (!empty($district_name)) {
                $data['district_name'] = $district_name;
            }
        }

        $data['address_default'] = $data['province_name'] . $data['city_name'] . $data['district_name'] . $data['region_name'];

        if (empty($data['longitude'])) {
            $coder = Geocoder::geocoding($data['address_default'] . $data['address']);
            Address::where('id', $data['id'])->update(['longitude' => $coder['longitude'], 'latitude' => $coder['latitude']]);
        }

        return $this->json(['data' => $data]);
    }
    public function getinfobyid()
    {
        $id = input('get.id', '', 'intval');
        $where['id'] = $id;
        $where['weid'] = weid();
        $where['uid'] = UID();
        $data = Address::where($where)
            ->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }


        if (empty($data['province_name'])) {
            $province_name = Area::get_area_name($data['province_id']);
            if (!empty($province_name)) {
                $data['province_name'] = $province_name;
            }
        }

        if (empty($data['city_name'])) {
            $city_name = Area::get_area_name($data['city_id']);
            if (!empty($city_name)) {
                $data['city_name'] = $city_name;
            }
        }

        if (empty($data['district_name'])) {
            $district_name = Area::get_area_name($data['district_id']);
            if (!empty($district_name)) {
                $data['district_name'] = $district_name;
            }
        }

        $data['address_default'] = $data['province_name'] . $data['city_name'] . $data['district_name'] . $data['region_name'];

        if (empty($data['longitude'])) {
            $coder = Geocoder::geocoding($data['address_default'] . $data['address']);
            Address::where('id', $data['id'])->update(['longitude' => $coder['longitude'], 'latitude' => $coder['latitude']]);
        }

        return $this->json(['data' => $data]);
    }

    public function list()
    {
        $uid = UID();
        $where['weid'] = weid();
        $where['uid'] = $uid;

        $query = Address::where($where);

        $Member = Member::find($uid);
        if (!empty($Member)) {
            $Member = $Member->toArray();
            $MemberAuthGroup = MemberAuthGroup::find($Member['gid']);
            if (!empty($MemberAuthGroup)) {
                $MemberAuthGroup = $MemberAuthGroup->toArray();
            }
        }
        if ($MemberAuthGroup['is_bindingaddress'] == 1) {
            $query->where('is_bindingaddress', 1);
        }

        $query->order('isDefault desc');

        $data = $query->select()->toArray();

        foreach ($data as &$area) {

            if (empty($area['province_name'])) {
                $province_name = Area::get_area_name($area['province_id']);
                if (!empty($province_name)) {
                    $area['province_name'] = $province_name;
                }
            }
    
            if (empty($area['city_name'])) {
                $city_name = Area::get_area_name($area['city_id']);
                if (!empty($city_name)) {
                    $area['city_name'] = $city_name;
                }
            }
    
            if (empty($area['district_name'])) {
                $district_name = Area::get_area_name($area['district_id']);
                if (!empty($district_name)) {
                    $area['district_name'] = $district_name;
                }
            }


            $area['address_detail'] = $area['province_name'] . $area['city_name'] . $area['district_name'] . $area['region_name'];
        }
        return $this->json(['data' => $data, 'is_bindingaddress' => $MemberAuthGroup['is_bindingaddress']]);
    }
}
