<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Config;
use app\model\HousingEstate;
use app\model\Tuanzhang;
use think\facade\Db;

class HousingestateController extends Base
{

    public function list()
    {
        $keyword = input('post.keyword', '', 'serach_in');
        $configtuanzhang = Config::getconfig('tuanzhang');

        $latitude = input('post.latitude', '', 'serach_in'); //纬度信息
        $longitude = input('post.longitude', '', 'serach_in'); //经度信息

        if (!empty($longitude) && !empty($latitude)) {
            $sql = "select * from (select id,weid,sort,tzid,title,latitude,longitude,province_name,city_name,district_name,area_name,house_number,image,status, ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude*PI()/180-`latitude`*PI()/180)/2),2)+COS($latitude*PI()/180)*COS(`latitude`*PI()/180)*POW(SIN(($longitude*PI()/180-`longitude`*PI()/180)/2),2)))*1000) AS distance from " . (new HousingEstate)->getTable() . " order by distance asc ) as a where status=1 ";
        } else {
            $sql = "select * from " . (new HousingEstate)->getTable() . " where status=1 ";
        }

        $sql .=  " and `weid` = " . weid();

        if (trim($keyword)) {
            $sql .=  " and `title` LIKE '%" . $keyword . "%'";
        }

        if ($configtuanzhang['is_city_housingestate'] == 1) {

            if (!empty($this->userInfo['cityinfo']['province_name'])) {
                $sql .=  " and `province_name` = '" . $this->userInfo['cityinfo']['province_name']."'";
            }
            if (!empty($this->userInfo['cityinfo']['city_name'])) {
                $sql .=  " and `city_name` = '" . $this->userInfo['cityinfo']['city_name']."'";
            }
            if (!empty($this->userInfo['cityinfo']['district_name'])) {
                $sql .=  " and `district_name` = '" . $this->userInfo['cityinfo']['district_name']."'";
            }
        }

        $data = Db::query($sql);

        foreach ($data as &$vo) {
            if (!empty($vo['tzid'])) {
                $Tuanzhang = Tuanzhang::find($vo['tzid']);
                if (!empty($Tuanzhang)) {
                    $vo['tz_touxiang'] = toimg($Tuanzhang['touxiang']);
                    $vo['community_title'] = $Tuanzhang['community_title'];
                    $vo['tz_title'] = $Tuanzhang['title'];
                    $vo['tz_tel'] = $Tuanzhang['tel'];
                }
            }

            $vo['image'] = toimg($vo['image']);
            $vo['distance'] = round(($vo['distance']) / 1000, 1);
        }
        $res['sql'] = $sql;
        $res['data'] = $data;
        return $this->json(['data' => $res]);
    }
}
