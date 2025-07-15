<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Store;
use app\model\StoreImage;
use app\model\Users;
use app\model\Tuanzhang;
use app\model\Config;
use app\model\Category;
use app\model\Goods;
use think\facade\Db;

class StoreController extends Base
{
    public function list()
    {
        $config = Config::getconfig();
        if (empty($config['storedistance'])) {
            $config['storedistance'] = 10;
        }
        if (!\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $cid = input('post.cid', '', 'serach_in');

            if (empty($cid)) {
                $cid = input('get.cid', '', 'serach_in');
            }
            $keyword = input('post.keyword', '', 'serach_in');

            $longitude = input('post.longitude', '', 'serach_in'); //经度信息
            $latitude = input('post.latitude', '', 'serach_in'); //纬度信息
            if (!empty($longitude) && !empty($latitude)) {
                $sql = "select * from (select id,cate_ids,weid,sort,title,tzid,latitude,longitude,owner_name,region_name,province_name,city_name,district_name,address,store_logo,status, ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude*PI()/180-`latitude`*PI()/180)/2),2)+COS($latitude*PI()/180)*COS(`latitude`*PI()/180)*POW(SIN(($longitude*PI()/180-`longitude`*PI()/180)/2),2)))*1000) AS distance from " . (new Store)->getTable() . " order by distance asc ) as a where a.distance<=" . ($config['storedistance'] * 1000) . " and status=1";
            } else {
                $sql = "select * from " . (new Store)->getTable() . " where status=1";
            }

            $sql .=  " and `weid` = " . weid();
            if (trim($keyword)) {
                $sql .=  " and (`title` LIKE '%" . $keyword . "%' OR address LIKE '%" . $keyword . "%' OR province_name LIKE '%" . $keyword . "%' OR city_name LIKE '%" . $keyword . "%' OR district_name LIKE '%" . $keyword . "%') ";
            }

            if ($cid) {
                $sql .=  " and FIND_IN_SET('" . $cid . "',cate_ids) ";
            }

            //分页
            $sqlpage =  getsqlpage();
            $sql .=  " LIMIT " . $sqlpage['start'] . "," . $sqlpage['limit'];

            //$sql .=  " order by sort asc,id desc";

            $data = Db::query($sql);

            foreach ($data as &$vo) {
                if (!empty($vo['tzid'])) {
                    if (empty($vo['store_logo']) ||  empty($vo['title'])) {
                        $Tuanzhang = Tuanzhang::find($vo['tzid']);
                        if (!empty($Tuanzhang)) {
                            $vo['store_logo'] = $Tuanzhang['touxiang'];
                            $vo['title'] = $Tuanzhang['community_title'];
                        }
                    }
                }
                $vo['store_logo'] = toimg($vo['store_logo']);
                $vo['distance'] = round(($vo['distance']) / 1000, 1);
            }
        }
        return $this->json(['data' => $data]);
    }

    public function detail($id)
    {
        $data = Store::find($id);

        if (!empty($data)) {
            $data = $data->toArray();
        }

        if (!empty($data['tzid'])) {
            if (empty($data['store_logo']) ||  empty($data['title'])) {
                $Tuanzhang = Tuanzhang::find($data['tzid']);
                if (!empty($Tuanzhang)) {
                    $data['store_logo'] = $Tuanzhang['touxiang'];
                    $data['title'] = $Tuanzhang['community_title'];
                }
            }
        }

        $data['store_banner'] = StoreImage::where(['sid' => $id, 'ptype' => 'banner'])
            ->field('image')
            ->order('sort asc')
            ->select()
            ->toArray();

        $data['content_img'] = StoreImage::where(['sid' => $id, 'ptype' => 'content'])
            ->field('image')
            ->order('sort asc')
            ->select()
            ->toArray();

        if (!empty($data['tzid'])) {

            $Tuanzhang = Tuanzhang::find($data['tzid']);
            if (!empty($Tuanzhang)) {
                if (empty($data['store_logo'])) {
                    $data['store_logo'] = $Tuanzhang['touxiang'];
                }
                if (empty($data['title'])) {
                    $data['title'] = $Tuanzhang['community_title'];
                }

                if (empty($data['content'])) {
                    $data['content'] = $Tuanzhang['introduction'];
                }
            }
        }

        $data['storeconfig']  = Config::getconfig('store');

        $data['create_time'] = time_ymd($data['create_time']);

        $data['address'] =  $data['city_name'] . $data['district_name'] . $data['house_number'];

        return $this->json(['data' => $data]);
    }

    public function goodslist()
    {
        $sid = input('get.sid', '', 'serach_in');
        $catdata = Category::getlist(['pid' => 0, 'status' => 1]);
        foreach ($catdata as $vo) {

            $vo['goodslist'] = Goods::getGoodsBycat(['sid' => $sid, 'cat' => $vo['id'], 'count' => 100]);

            if ($vo['goodslist']) {
                $data[] = $vo;
            }
        }
        return $this->json(['data' => $data]);
    }
}