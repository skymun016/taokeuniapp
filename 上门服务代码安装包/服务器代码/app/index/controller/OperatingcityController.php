<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Geocoder;
use app\model\Config;
use app\model\Operatingcity;
use app\model\HousingEstate;
use app\model\Tuanzhang;
use app\model\Address;
use think\facade\Db;

class OperatingcityController extends Base
{

    public function getcity()
    {
        $postdata = input('post.');
        $Configtuanzhang = Config::getconfig('tuanzhang');
        $configoperatingcity = Config::getconfig('operatingcity');
        $Configdata = Config::getconfig();
        $is_nulldate = 0;

        /*
        if ($this->userInfo['city_info_expire_time']) {
            //10分钟内不重新获取
            if ($this->userInfo['city_info_expire_time'] + 600 < time() || empty($this->userInfo['cityinfo']['cityName'])) {
                $this->userInfo['cityinfo'] = [];
                $this->userInfo['city_info_expire_time'] = time();
            }
        } else {
            $this->userInfo['cityinfo'] = [];
            $this->userInfo['city_info_expire_time'] = time();
        }*/

        if (!empty($postdata['he_id'])) {
            $this->userInfo['cityinfo']['he_id'] = $postdata['he_id'];
        }

        if (!empty($postdata['tz_id'])) {
            $this->userInfo['cityinfo']['tz_id'] = $postdata['tz_id'];
            $Tuanzhang = Tuanzhang::find($postdata['tz_id']);
            if (!empty($Tuanzhang)) {
                $Tuanzhang = $Tuanzhang->toArray();
                $this->userInfo['cityinfo']['tz_id'] = $Tuanzhang['id'];
                $this->userInfo['cityinfo']['tuanzhangName'] = $Tuanzhang['title'];
                $this->userInfo['cityinfo']['community_title'] = $Tuanzhang['community_title'];
                $this->userInfo['cityinfo']['tuanzhangtouxiang'] = toimg($Tuanzhang['touxiang']);
            }
        }

        if (!empty($postdata['city_id'])) {
            $Operatingcity = Operatingcity::find($postdata['city_id']);
            if (!empty($Operatingcity)) {
                $Operatingcity = $Operatingcity->toArray();

                unset($this->userInfo['cityinfo']['he_id']);
                unset($this->userInfo['cityinfo']['tuanzhangName']);
                unset($this->userInfo['cityinfo']['community_title']);
                unset($this->userInfo['cityinfo']['tuanzhangtouxiang']);
                unset($this->userInfo['cityinfo']['tz_id']);

                $this->userInfo['cityinfo']['ocid'] = $Operatingcity['id'];
                $this->userInfo['cityinfo']['province_name'] = $Operatingcity['province_name'];
                $this->userInfo['cityinfo']['city_name'] = $Operatingcity['city_name'];
                $this->userInfo['cityinfo']['district_name'] = $Operatingcity['district_name'];

                $this->userInfo['cityinfo']['cityName'] = $this->userInfo['cityinfo']['city_name'];

                $this->userInfo['cityinfo']['latitude'] = $Operatingcity['latitude'];
                $this->userInfo['cityinfo']['longitude'] = $Operatingcity['longitude'];
            }
        } elseif (!empty($postdata['he_id']) && $Configtuanzhang['tuanzhang_open'] == 1) {

            $HousingEstate = HousingEstate::find($postdata['he_id']);
            if (!empty($HousingEstate)) {
                $HousingEstate = $HousingEstate->toArray();
                $this->userInfo['cityinfo']['he_id'] = $postdata['he_id'];
                $this->userInfo['cityinfo']['housingName'] = $HousingEstate['title'];

                $this->userInfo['cityinfo']['province_name'] = $HousingEstate['province_name'];
                $this->userInfo['cityinfo']['city_name'] = $HousingEstate['city_name'];
                $this->userInfo['cityinfo']['district_name'] = $HousingEstate['district_name'];

                $this->userInfo['cityinfo']['latitude'] = $HousingEstate['latitude'];
                $this->userInfo['cityinfo']['longitude'] = $HousingEstate['longitude'];

                $Tuanzhang = Tuanzhang::find($HousingEstate['tzid']);
                if (!empty($Tuanzhang)) {
                    $Tuanzhang = $Tuanzhang->toArray();
                    $this->userInfo['cityinfo']['tz_id'] = $Tuanzhang['id'];
                    $this->userInfo['cityinfo']['tuanzhangName'] = $Tuanzhang['title'];
                    $this->userInfo['cityinfo']['community_title'] = $Tuanzhang['community_title'];
                    $this->userInfo['cityinfo']['tuanzhangtouxiang'] = toimg($Tuanzhang['touxiang']);
                }
            }
        } elseif (!empty($postdata['latitude']) && !empty($postdata['longitude']) &&  empty($this->userInfo['cityinfo']['ocid'])) {
            $this->userInfo['cityinfo']['latitude'] = $postdata['latitude'];
            $this->userInfo['cityinfo']['longitude'] = $postdata['longitude'];

            if (!empty($postdata['address'])) {
                $resarea = Address::Address2area($postdata['address']);
            }
            if ($resarea['province_name'] && $resarea['city_name'] && $resarea['district_name']) {

                $this->userInfo['cityinfo']['province_name'] = $resarea['province_name'];
                $this->userInfo['cityinfo']['city_name'] = $resarea['city_name'];
                $this->userInfo['cityinfo']['district_name'] = $resarea['district_name'];
                $this->userInfo['cityinfo']['district_name'] = $resarea['district_name'];
            } else {
                $data = Geocoder::reverse_geocoding($postdata['latitude'], $postdata['longitude']);
                $this->userInfo['cityinfo']['province_name'] = $data['addressComponent']['province'];
                $this->userInfo['cityinfo']['city_name'] = $data['addressComponent']['city'];
                $this->userInfo['cityinfo']['is_Geocoder'] = 1;
            }
        }

        $this->userInfo['cityinfo']['cityName'] = $this->userInfo['cityinfo']['city_name'];
        if ($Configdata['locationscope'] == 3 && $this->userInfo['cityinfo']['district_name']) {

            $this->userInfo['cityinfo']['cityName'] = $this->userInfo['cityinfo']['district_name'];
        }
        if ($Configdata['locationscope'] == 1 && $this->userInfo['cityinfo']['province_name']) {
            $this->userInfo['cityinfo']['cityName'] = $this->userInfo['cityinfo']['province_name'];
        }

        if ($Configdata['locationscope'] == 3 && $this->userInfo['cityinfo']['district_name']) {

            $Operatingcity = Operatingcity::where('status',1)->where('areatype', $Configdata['locationscope'])->where('district_name', $this->userInfo['cityinfo']['district_name'])->find();
            if (empty($Operatingcity)) {
                $Operatingcity = Operatingcity::where('status',1)->whereNotNull('areatype')->where('district_name', $this->userInfo['cityinfo']['district_name'])->find();
            }

            if (!empty($Operatingcity)) {
                $Operatingcity = $Operatingcity->toArray();
                $this->userInfo['cityinfo']['ocid'] = $Operatingcity['id'];
            }
        } elseif ($Configdata['locationscope'] == 1 && $this->userInfo['cityinfo']['province_name']) {

            $Operatingcity = Operatingcity::where('status',1)->where('areatype', $Configdata['locationscope'])->where('province_name', $this->userInfo['cityinfo']['province_name'])->find();
            if (!empty($Operatingcity)) {
                $Operatingcity = $Operatingcity->toArray();
                $this->userInfo['cityinfo']['ocid'] = $Operatingcity['id'];
            }
        } else {
            if ($this->userInfo['cityinfo']['city_name']) {
                $Operatingcity = Operatingcity::where('status',1)->where('areatype', $Configdata['locationscope'])->where('city_name', $this->userInfo['cityinfo']['city_name'])->find();
                if (!empty($Operatingcity)) {
                    $Operatingcity = $Operatingcity->toArray();
                    $this->userInfo['cityinfo']['ocid'] = $Operatingcity['id'];
                }
            }
        }

        $this->setAppToken($this->userInfo, $this->getAppToken());
        if ($postdata['is_index'] == 1 && $Configdata['is_close_getposition'] == 1) {
            $is_close_getposition  = 1;
        } else {
            $is_close_getposition  = 0;
        }
        if ($this->userInfo['cityinfo']['cityName'] == null) {
            unset($this->userInfo['cityinfo']['cityName']);
        }
        if (empty($this->userInfo['cityinfo']['cityName'])) {
            unset($this->userInfo['cityinfo']['cityName']);
        }
        if (!empty($this->userInfo['cityinfo'])) {
            $this->userInfo['cityinfo']['locationscope'] = $Configdata['locationscope'];
            $is_nulldate = 1;
        }
        $this->userInfo['cityinfo']['operatingcityis_limit'] = $configoperatingcity['is_limit'];

        return $this->json([
            'data' => $this->userInfo['cityinfo'],
            'is_nulldate' => $is_nulldate,
            'is_close_getposition' => $is_close_getposition
        ]);
    }
    public function list()
    {
        $keyword = input('post.keyword', '', 'serach_in');
        $config = Config::getconfig();
        if (empty($config['locationscope'])) {
            $config['locationscope'] = 3;
        }

        $latitude = input('post.latitude', '', 'serach_in'); //纬度信息
        $longitude = input('post.longitude', '', 'serach_in'); //经度信息
        if (!empty($longitude) && !empty($latitude)) {
            $sql = "select * from (select id,weid,sort,title,areatype,latitude,longitude,province_name,city_name,district_name,area_name,house_number,status, ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude*PI()/180-`latitude`*PI()/180)/2),2)+COS($latitude*PI()/180)*COS(`latitude`*PI()/180)*POW(SIN(($longitude*PI()/180-`longitude`*PI()/180)/2),2)))*1000) AS distance from " . (new Operatingcity)->getTable() . " order by distance asc ) as a where status=1 ";
        } else {
            $sql = "select * from " . (new Operatingcity)->getTable() . " where status=1 ";
        }
        $sql .=  " and `weid` = " . weid();

        $sql .=  " and `areatype` = " . $config['locationscope'];

        if (trim($keyword)) {
            $sql .=  " and `title` LIKE '%" . $keyword . "%'";
        }

        //var_dump($sql);

        $data = Db::query($sql);

        foreach ($data as &$vo) {
            $vo['image'] = toimg($vo['image']);
            $vo['distance'] = round(($vo['distance']) / 1000, 1);
        }
        $res['data'] = $data;
        $res['sql'] = $sql;
        return $this->json(['data' => $res]);
    }
}
