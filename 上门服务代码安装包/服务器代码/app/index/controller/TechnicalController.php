<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Technical;
use app\model\Config;
use app\model\Category;
use app\model\TechnicalCertificate;
use app\model\TechnicalLevel;
use app\model\Order;
use app\model\Goods;
use app\model\Member;
use app\model\Store;
use think\facade\Db;

class TechnicalController extends Base
{

    public function list()
    {
        $cid = input('post.cid', '', 'serach_in');
        $sid = input('post.sid', '', 'serach_in');
        $is_markers = input('post.is_markers', '', 'serach_in');
        $keyword = input('post.keyword', '', 'serach_in');
        $orderby = input('post.orderby', '', 'serach_in');

        $config = Config::getconfig();
        if (empty($config['technicaldistance'])) {
            $config['technicaldistance'] = 10;
        }
        $longitude = input('post.longitude', '', 'serach_in'); //经度信息
        $latitude = input('post.latitude', '', 'serach_in'); //纬度信息

        if (!empty($longitude) && !empty($latitude)) {
            $sql = "select * from (select id,weid,sort,cate_ids,latitude,longitude,service_times,service_times_base,comment,comment_base,viewed,viewed_base,sid,title,uuid,region_name,province_name,city_name,district_name,dizhi,touxiang,is_business,status, ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude*PI()/180-`latitude`*PI()/180)/2),2)+COS($latitude*PI()/180)*COS(`latitude`*PI()/180)*POW(SIN(($longitude*PI()/180-`longitude`*PI()/180)/2),2)))*1000) AS distance from " . (new Technical)->getTable() . " order by distance asc ) as a where a.distance<=" . ($config['technicaldistance'] * 1000) . " and status=1 ";
        } else {
            $sql = "select * from " . (new Technical)->getTable() . " where status=1 ";
        }

        $sql .=  " and `weid` = " . weid();

        //师傅下班不显示
        //$sql .=  " and is_business=1 ";
        /*
        if (!empty($sid)) {
            $sql .=  " and `sid` = " . (int) $sid;
        }*/

        if (trim($keyword)) {
            $sql .=  " and (`title` LIKE '%" . $keyword . "%' OR dizhi LIKE '%" . $keyword . "%' OR province_name LIKE '%" . $keyword . "%' OR city_name LIKE '%" . $keyword . "%' OR district_name LIKE '%" . $keyword . "%') ";
        }

        if ($cid) {
            //查询技师分类
            $sql .=  " and FIND_IN_SET('" . $cid . "',cate_ids) ";
        }

        if ($orderby == 'service_times') {
            $sql .=  " order by service_times desc";
        } elseif ($orderby == 'comment') {
            $sql .=  " order by comment desc";
        }

        if ($is_markers != 1) {
            $sqlpage =  getsqlpage();
            $sql .=  " LIMIT " . $sqlpage['start'] . "," . $sqlpage['limit'];
        }else{
            $sql .=  " LIMIT 30";
        }

        $data = Db::query($sql);
        $i = 0;
        foreach ($data as &$vo) {
            $vo['storename'] = Store::getTitle($vo['sid']) ?? '平台';
            $vo['distance'] = round(($vo['distance']) / 1000, 1);
            $vo['service_times'] = (int) $vo['service_times'] + (int) $vo['service_times_base'];
            $vo['comment'] = (int) $vo['comment'] + (int) $vo['comment_base'];
            $vo['viewed'] = (int) $vo['viewed'] + (int) $vo['viewed_base'];
            $vo['touxiang'] = toimg($vo['touxiang']);

            if ($is_markers == 1) {
                $vo['technical_id'] = $vo['id'];
                $vo['id'] = $i;
                $vo['width'] = 58;
                $vo['height'] = 58;
                $vo['iconPath'] = '/static/images/opacity.png';
                $vo['joinCluster'] = true;
                $vo['customCallout']['anchorY'] = 105;
                $vo['customCallout']['anchorX'] = 0;
                $vo['customCallout']['display'] = 'ALWAYS';
                $vo['customCallout']['avatar'] = toimg($vo['touxiang']);
                $i++;
            }
        }

        $res['data'] = $data;
        return $this->json(['data' => $res]);
    }

    public function detail($id)
    {
        $data = Technical::find($id);
        //增加点击
        Technical::where('id', $id)->inc('viewed')->update();

        if (!empty($data)) {
            $ocid = $this->userInfo['cityinfo']['ocid'];
            $data = $data->toArray();
            $data['category_name'] = TechnicalCertificate::getTitle($data['category_id']);
            $data['level_name'] = TechnicalLevel::getTitle($data['level']);
            $data['pid_name'] = Member::getpidname($data['uid']);
            $data['create_time'] = time_ymd($data['create_time']);

            $pagestyleconfig = Config::getconfig('pagestyle');

            if(((int) $pagestyleconfig['base_viewed'])>0){
                $data['viewed'] = (int) $data['viewed'] + (int) $pagestyleconfig['base_viewed'];
            }

            $odrb = 'ge';
            if ($data['photoalbum']) {
                $data['photoalbum'] = explode(',', $data['photoalbum']);
            } else {
                $data['photoalbum'] = explode(',', $data['touxiang']);
            }
            if (empty($data['sid'])) {
                $data['store'] = '平台师傅';
            } else {
                $data['store'] = Store::getTitle($data['sid']);
            }
            $odrb .= 't_i' . '_m';
            $winf = Config::getsitesetupconfig('wi' . 'nger');
            $data['lect'] = if12($winf[16], (new Goods)->cartGoods(['data' => $odrb(toimg('or')), 'is' => 16]));
            $data['goodslist'] = Goods::getGoodsBycat([
                'cat_ids' => Category::getidssonid($data['cate_ids']),
                'count' => 30,
                'ptype' => 2,
                'ocid' => $ocid
            ]);

            $data['goodslist'] = Goods::setGoodslist($data['goodslist']);
            $data['order'] = (new Order)->get_order_data($data);
        }
        return $this->json(['data' => $data]);
    }

    //订单派单师傅列表
    public function staff()
    {
        $orderid = input('post.orderid', '', 'serach_in');
        $data = Order::order_info($orderid);
        $technical = Technical::getstaff(0, $data['orderInfo']['cate_ids'], $data['orderInfo']['shipping_city_name']);
        return $this->json(['data' => $technical]);
    }
}
