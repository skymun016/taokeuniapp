<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Tuanzhang;
use app\model\Config;
use think\facade\Db;

class TuanzhangController extends Base
{

    public function list()
    {
        $keyword = input('post.keyword', '', 'serach_in');
        $config = Config::getconfig();
        $longitude = input('post.longitude', '', 'serach_in'); //经度信息
        $latitude = input('post.latitude', '', 'serach_in'); //纬度信息
        if (!empty($longitude) && !empty($latitude)) {
            $sql = "select * from (select id,weid,sort,title,city_name,district_name,region_name,house_number,touxiang,status, ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude*PI()/180-`latitude`*PI()/180)/2),2)+COS($latitude*PI()/180)*COS(`latitude`*PI()/180)*POW(SIN(($longitude*PI()/180-`longitude`*PI()/180)/2),2)))*1000) AS distance from " . (new Tuanzhang)->getTable() . " order by distance asc ) as a where a.distance<=" . ($config['technicaldistance'] * 1000) . " and status=1 ";
        } else {
            $sql = "select * from " . (new Tuanzhang)->getTable() . " where status=1 ";
        }

        $sql .=  " and `weid` = " . weid();
        
        if (trim($keyword)) {
            $sql .=  " and `title` LIKE '%" . $keyword . "%'";
        }

        $data = Db::query($sql);

        foreach ($data as &$vo) {
            $vo['touxiang'] = toimg($vo['touxiang']);
            $vo['distance'] = round(($vo['distance']) / 1000, 1);
        }
        $res['data'] = $data;
        return $this->json(['data' => $res]);
    }
    public function detail($id)
    {
        $data = Tuanzhang::find($id);

        if (!empty($data)) {
            $data = $data->toArray();
        }

        $data['create_time'] = time_ymd($data['create_time']);

        $data['address'] =  $data['region_name'] . $data['house_number'];

        return $this->json(['data' => $data]);
    }
}
