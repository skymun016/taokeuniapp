<?php

namespace app\model;

use think\Model;

class Technical extends Model
{


    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'technical';

    public static function getTitle($uuid)
    {
        $data = self::where('uuid', $uuid)->whereNotNull('uuid')->find();
        return $data->title;
    }
    public static function getTitlebyuid($uid)
    {
        $data = self::where('uid', $uid)->find();
        return $data->title;
    }
    public static function getcityName($uuid)
    {
        $data = self::where('uuid', $uuid)->whereNotNull('uuid')->find();
        return $data->city_name;
    }
    public static function getTel($uuid)
    {
        $data = self::where('uuid', $uuid)->whereNotNull('uuid')->find();
        return $data->tel;
    }

    public static function getInfobyuid($uid)
    {
        $uuid = UuidRelation::getuuid($uid, 'technical');
        if (!empty($uuid)) {
            $data = Technical::where(['uuid' => $uuid])->find();
            if (!empty($data)) {
                $data = $data->toArray();
            } else {
                $data = Technical::where(['uid' => $uid])->find();
                if (!empty($data)) {
                    $data = $data->toArray();
                    if (!empty($uuid)) {
                        Technical::where('id', $data['id'])->update(['uuid' => $uuid]);
                    }
                }
            }
        }
        return $data;
    }

    public static function getInfo($uuid)
    {
        $data = self::where('uuid', $uuid)->whereNotNull('uuid')->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }
        return $data;
    }

    public static function conversion($vo)
    {
        $vo['cate_ids'] = Category::getmultiple($vo['cate_ids']) ?? '无';
        $vo['certificate_ids'] = TechnicalCertificate::getmultiple($vo['certificate_ids']) ?? '无';
        $vo['level'] = TechnicalLevel::getTitle($vo['level']) ?? '初级';
        $vo['sid'] = Store::getTitle($vo['sid']) ?? '平台';

        if (!empty($vo['uuid'])) {
            $vo['username'] = Users::getusername($vo['uuid']);
        }

        $vo['touxiang'] = toimg($vo['touxiang']);
        if (!empty($vo['photoalbum'])) {
            $vo['photoalbum'] = toimg(explode(',', $vo['photoalbum'])[0]);
        }

        $vo['region_name'] = $vo['province_name'] . $vo['city_name'] . $vo['district_name'];
        $vo['end_time'] = time_format($vo['end_time']) ?? '长期';
        $vo = RegisterField::conversion($vo);
        return $vo;
    }

    public static function getpcarray($sid)
    {
        $data = self::field('uuid,title')->where(['weid' => weid(), 'sid' => (int)$sid, 'status' => 1])->select()->toArray();
        $datalist = [];
        foreach ($data as $key => $vo) {
            if (!empty($vo['uuid'])) {
                $datalist[$key]['val'] = $vo['uuid'];
                $datalist[$key]['key'] = $vo['title'];
            }
        }
        return $datalist;
    }
    public static function getstaff($sid, $cate_ids, $cityName)
    {
        $query = new self;
        if (!empty($cate_ids)) {
            foreach ((array) (explode(',', $cate_ids)) as $catevo) {
                if ($catevo) {
                    $query->whereOr('cate_ids', 'find in set', $catevo);
                }
            }
        }

        $orderidsarray = $query->field('id')->select()->toArray();
        foreach ($orderidsarray as $ovo) {
            if (empty($orderids)) {
                $orderids = $ovo['id'];
            } else {
                $orderids = $orderids . "," . $ovo['id'];
            }
        }

        $resquery = self::field('id,city_name,level,uuid,cate_ids,title')->where('is_business', 1)->where('id', 'in', $orderids)->where(['weid' => weid(), 'city_name' => $cityName, 'status' => 1]);

        if (!empty($sid)) {
            $resquery->where(['sid' => (int)$sid]);
        }

        $data = $resquery->select()->toArray();
        return $data;
    }
    public static function getpcarraydetailed($sid, $cate_ids, $cityName)
    {

        $data = self::getstaff($sid, $cate_ids, $cityName);

        $datalist = [];
        foreach ($data as $key => $vo) {
            if (!empty($vo['uuid'])) {
                $strlevel = TechnicalLevel::getTitle($vo['level']) ?? '初级';
                $datalist[$key]['val'] = $vo['uuid'];
                $datalist[$key]['key'] = $vo['title'] . '--[' . $strlevel . ']' . '[已接单量：' . OrderStaff::ordercount($vo['uuid']) . '][可接服务：' . Category::getmultiple($vo['cate_ids']) . ']';
            }
        }
        return $datalist;
    }
    public static function gelist_bycitycate($orderInfo)
    {
        $where['weid'] = weid();
        $where['status'] = 1;
        $Configdata = Config::getconfig();

        if (empty($Configdata['locationscope'])) {
            $Configdata['locationscope'] = 3;
        }
        if ($Configdata['locationscope'] == 3) {
            $where['district_name'] = $orderInfo['shipping_district_name'];
        } else if ($Configdata['locationscope'] == 2) {
            $where['city_name'] = $orderInfo['shipping_city_name'];
        } else if ($Configdata['locationscope'] == 1) {
            $where['province_name'] = $orderInfo['shipping_province_name'];
        }

        $query = Technical::where($where);

        $query->where('cate_ids', 'find in set', $orderInfo['cate_ids']);

        $Sort = 'id desc';

        $data = $query->order($Sort)->select()->toArray();

        return $data;
    }
    public static function getdiy_bycat($cat = 0, $count = 4, $Sort = "all", $ocid = 0)
    {
        $where['weid'] = weid();
        $where['status'] = 1;

        if ($cat > 0) {
            if (!empty($cat)) {
                $where['category_id'] = Category::getsonid($cat);
            }
        }

        $query = Technical::where($where);

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
                }
            });
        }

        if ($Sort == "all") {
            $Sort = 'sort asc,id desc';
        } elseif ($Sort == "sales") {
            $Sort = 'service_times desc';
        } elseif ($Sort == "price") {
            $Sort = 'create_time asc';
        }

        $data = $query->limit($count)
            ->order($Sort)->select()->toArray();

        return $data;
    }

    public static function setIncome($order_info)
    {
        $uuid =  OrderStaff::getuuid($order_info['id']);
        if (!empty($uuid)) {
            $Technical = Technical::where(['uuid' => $uuid])->find();
            if (!empty($Technical)) {
                $Technical = $Technical->toArray();
            }

            if (!empty($Technical['status'])) {
                $percent = TechnicalLevel::getPercent($Technical['level']);
                if (empty($percent)) {
                    $percent = Config::getconfig('technical')['return_percent'];
                }
                //如果是商户的师傅，分佣比较需要减去商户的佣金
                if (!empty($Technical['sid'])) {
                    $Store = Store::find($Technical['sid']);
                    if (!empty($Store)) {
                        $Store = $Store->toArray();
                        $storepercent = StoreLevel::getPercent($Store['level']);
                        if (empty($storepercent)) {
                            $storepercent = Config::getconfig('store')['return_percent'];
                        }
                        if (!empty($storepercent)) {
                            $percent = $percent - $storepercent;
                        }
                    }
                }

                if ($percent > 0) {

                    if (empty($order_info['cat_id'])) {
                        $income = OrderGoods::getCommission($order_info, '', $percent);
                    } else {
                        $income = $order_info['total'];
                        $income = ($income * percent_to_num($percent));
                    }
                    


                    $return_percent = $percent;
                    if ($income > 0) {

                        if ($income > 0 && $income < 0.01) {
                            $income = 0.01;
                        }

                        $TechnicalIncomelog = TechnicalIncomelog::where([
                            'uuid' => $uuid,
                            'weid' => $order_info['weid'],
                            'order_id' =>  $order_info['id'],
                        ])->find();

                        if (empty($TechnicalIncomelog)) {

                            Technical::where('uuid', $uuid)->inc('income', $income)->update();
                            Technical::where('uuid', $uuid)->inc('total_income', $income)->update();

                            $incomedata['uuid'] = $uuid;
                            $incomedata['ptype'] = 1;
                            $incomedata['weid'] = $order_info['weid'];
                            $incomedata['order_id'] = $order_info['id'];
                            $incomedata['order_num_alias'] = $order_info['order_num_alias'];
                            $incomedata['buyer_id'] = $order_info['uid'];
                            $incomedata['income'] = $income;
                            $incomedata['return_percent'] = floatval($return_percent);
                            $incomedata['percentremark'] = $order_info['total'] . '% x' . $percent . '%';
                            $incomedata['order_total'] = $order_info['total'];
                            $incomedata['pay_time'] = $order_info['pay_time'];
                            $incomedata['month_time'] = date('m', time());
                            $incomedata['year_time'] = date('Y', time());
                            $incomedata['order_status_id'] = 2; //已付款

                            TechnicalIncomelog::create($incomedata);
                        }
                    }
                }
            }
        }
    }
}
