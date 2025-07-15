<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\House;
use app\model\HouseDescription;
use app\model\HouseImage;
use app\model\Config;
use app\model\Store;
use app\model\Operatingcity;

class HouseController extends Base
{
    public function index()
    {
        $weid = weid();
        $Configdata = Config::getconfig();
        $ocid = $this->userInfo['cityinfo']['ocid'];
        $serach['keyword'] = input('post.keyword', '', 'serach_in');
        $serach['ocid'] = input('post.ocid', '', 'serach_in');
        $serach['sid'] = input('post.sid', '', 'serach_in');
        $serach['news'] = input('post.news', '', 'serach_in');
        $priceOrder = input('post.priceOrder', '', 'serach_in');
        $salesOrder = input('post.salesOrder', '', 'serach_in');

        $query = House::where(['weid' => $weid, 'status' => 1]);

        if (!empty($priceOrder)) {
            $Sort = 'price ' . $priceOrder;
        } else {
            $Sort = 'sort asc,id desc';
        }
        if (!empty($serach['ocid'])) {
            $query->where('ocid', $serach['ocid']);
        }

        if (!empty($serach['sid'])) {
            $query->where('sid', $serach['sid']);
        } else {
            if (empty($Configdata['show_storegoods'])) {
                $query->where('sid', 0);
            }
        }

        if (!empty($serach['keyword'])) {
            $query->where('name', 'like', '%' . $serach['keyword'] . '%');
        }
        if (!empty($serach['news'])) {
            $query->where('is_new',  1);
        }

        if ($ocid) {
            $query->where(function ($q) use ($ocid) {
                $Operatingcity = Operatingcity::find($ocid);
                if (!empty($Operatingcity)) {
                    $Operatingcity = $Operatingcity->toArray();
                    if (empty($Operatingcity['areatype'])) {
                        $Operatingcity['areatype'] = 3;
                    }
                    if ($Operatingcity['areatype'] == 3) {
                        $q->where('district_name', $Operatingcity['district_name'])->whereOr('district_name', '');
                    } elseif ($Operatingcity['areatype'] == 2) {
                        $q->where('city_name', $Operatingcity['city_name'])->whereOr('city_name', '');
                    } elseif ($Operatingcity['areatype'] == 1) {
                        $q->where('province_name', $Operatingcity['province_name'])->whereOr('province_name', '');
                    }
                } else {
                    if (empty($serach['sid'])) {
                        $q->where('city_name', '');
                    }
                }
            });
        } else {
            if (empty($serach['sid'])) {
                $query->where('city_name', '');
            }
        }
        $res = $query->order($Sort)
            ->paginate(getpage())
            ->toArray();

        $data['sql'] = $query->getLastSql();
        if ($res['data']) {
            foreach ($res['data'] as &$vo) {
                $vo['image'] = toimg($vo['image']);
                if (!empty($serach['sid'])) {
                    if ($vo['sid'] == 0) {
                        $vo['name'] = '[自营]' . $vo['name'];
                    } else {
                        $vo['name'] = '[' . Store::getTitle($vo['sid']) . ']' . $vo['name'];
                    }
                }
            }
        }
        $data['data'] = $res;

        return $this->json($data);
    }

    public function detail($id)
    {
        //增加点击
        House::where('id', $id)->inc('viewed')->update();

        $goodsdata = House::getInfo($id);
        if (!empty($tuan['price'])) {
            $goodsdata['original_price'] = $goodsdata['price'];
            $goodsdata['price'] = $tuan['price'];
        }
        $data = $goodsdata;

        if (!empty($data['keyword'])) {
            $data['keyword'] = explode(',', $data['keyword']);
        } else {
            $data['keyword'] = [];
        }
        $ods = 'get';
        $picdataarray = HouseImage::where(['house_id' => $id])
            ->order('id asc')
            ->select()
            ->toArray();

        foreach ($picdataarray as &$vo) {
            $vo['pic'] = toimg($vo['image']);
        }

        $data['videourl'] =  toimg($data['videourl']);
        $description = HouseDescription::where(['house_id' => $id])->find();

        if ($data['quantity'] < 0) {
            $data['quantity'] = 0;
        }


        $data['pic'] = toimg($goodsdata['image']);

        $data['viewed'] = $goodsdata['viewed'] + $goodsdata['viewed_base'];

        $data['price'] = $goodsdata['price'];
        $data['content'] = \app\model\DomainReplace::setreplace(sethtmlimg($description->description));
        if (!empty($picdataarray)) {
            $data['pics'] = $picdataarray;
        } else {
            $data['pics'] = [];
        }

        return $this->json(['data' => $data]);
    }
}
