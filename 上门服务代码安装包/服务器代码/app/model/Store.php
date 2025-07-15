<?php

namespace app\model;

use think\Model;

class Store extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'store';

    public static function getTitle($id = '')
    {
        $mo = self::find($id);
        return $mo->title;
    }
    public static function getUuid($id = '')
    {
        $mo = self::find($id);
        return $mo->uuid;
    }

    public static function getStorebytzid($tzid)
    {
        $mo = self::where('tzid', $tzid)->find();
        if (!empty($mo)) {
            return $mo->toArray();
        } else {
            $mo = self::createTuanzhang($tzid);
            if (!empty($mo)) {
                return $mo->toArray();
            }
        }
    }
    public static function getidbytzid($tzid)
    {
        $mo = self::where('tzid', $tzid)->find();
        if (!empty($mo)) {
            return $mo->id;
        } else {
            $mo = self::createTuanzhang($tzid);
            return $mo->id;
        }
    }

    public static function createTuanzhang($tzid)
    {

        $Tuanzhang = Tuanzhang::find($tzid);

        if (!empty($Tuanzhang)) {
            $Tuanzhang = $Tuanzhang->toArray();
            self::create([
                'weid' => $Tuanzhang['weid'],
                'tzid' => $Tuanzhang['id'],
                'owner_name' => $Tuanzhang['title'],
                'title' => $Tuanzhang['community_title'],
                'tel' => $Tuanzhang['tel'],
                'store_logo' => $Tuanzhang['touxiang'],
                'latitude' => $Tuanzhang['latitude'],
                'longitude' => $Tuanzhang['longitude'],
                'region_name' => $Tuanzhang['region_name'],
                'province_name' => $Tuanzhang['province_name'],
                'city_name' => $Tuanzhang['city_name'],
                'district_name' => $Tuanzhang['district_name'],
                'house_number' => $Tuanzhang['house_number'],
                'cate_ids' => $Tuanzhang['cate_ids'],
                'status' => $Tuanzhang['status'],
            ]);
            return self::where('tzid', $tzid)->find();
        }
    }

    public static function getTitlebyuid($uuid = '')
    {
        $mo = self::where('uuid', $uuid)->find();
        return $mo->title;
    }
    public static function getInfobyuid($uid)
    {
        $uuid = UuidRelation::getuuid($uid, 'store');
        if (!empty($uuid)) {
            $data = Store::where(['uuid' => $uuid])->find();
            if (!empty($data)) {
                $data = $data->toArray();
            }
        }

        return $data;
    }
    public static function getpcarray()
    {
        $data = self::field('id,title')->where(['weid' => weid(), 'status' => 1])
            ->order('sort asc')
            ->select()->toArray();

        foreach ($data as $k => $v) {
            $array[$k]['val'] = $v['id'];
            $array[$k]['key'] = $v['title'];
        }
        return $array;
    }
    public static function conversion($vo)
    {
        if (empty($vo['level'])) {
            $vo['level'] = '未设置';
        } else {
            $vo['level'] = StoreLevel::getTitle($vo['level']);
        }

        if ($vo['end_time'] == 0) {
            $vo['end_time'] = '永久有效';
        } else {
            $vo['end_time'] = time_format($vo['end_time']);
        }
        $vo['cate_ids'] = Category::getmultiple($vo['cate_ids']) ?? '无';
        if (!empty($vo['uuid'])) {
            $vo['username'] = Users::getusername($vo['uuid']);
        }
        $vo['region_name'] = $vo['province_name'] . $vo['city_name'] . $vo['district_name'];
        $vo = RegisterField::conversion($vo);
        return $vo;
    }
    public static function getdiy_bycat($cat = 0, $count = 4, $Sort = "all", $ocid = 0)
    {
        $where['weid'] = weid();
        $where['status'] = 1;

        if ($cat > 0) {
            if (!empty($cat)) {
                $where['cate_ids'] = Category::getsonid($cat);
            }
        }

        $query = self::where($where);

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
                        $q->where('province_name', 'like', '%' . $Operatingcity['province_name'] . '%')->whereOr('province_name', '');
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
        }

        return $data;
    }

    public static function setIncome($order_info)
    {
        if (empty($order_info['sid'])) {
            $uid =  OrderStaff::getuuid($order_info['id']);
            if (!empty($uuid)) {
                $data = Technical::where('uuid', $uuid)->find();
                if (!empty($data)) {
                    $order_info['sid'] = $data->sid;
                }
            }
        }

        if (!empty($order_info['sid'])) {
            $Store = Store::find($order_info['sid']);
            if (!empty($Store)) {
                $Store = $Store->toArray();
            }

            if (!empty($Store['status'])) {
                $percent = StoreLevel::getPercent($Store['level']);

                if (empty($percent)) {
                    $percent = Config::getconfig('store')['return_percent'];
                }

                if ($percent > 0) {

                    if (empty($order_info['cat_id'])) {
                        $income = OrderGoods::getCommission($order_info, 'store', $percent);
                    } else {
                        $income = $order_info['total'];
                        $income = ($income * percent_to_num($percent));
                    }

                    $return_percent = $percent;
                    if ($income > 0) {

                        if ($income > 0 && $income < 0.01) {
                            $income = 0.01;
                        }
                        $StoreIncomelog = StoreIncomelog::where([
                            'sid' => $order_info['sid'],
                            'weid' => $order_info['weid'],
                            'order_id' =>  $order_info['id'],
                        ])->find();

                        if (empty($StoreIncomelog)) {

                            Store::where('id', $order_info['sid'])->inc('income', $income)->update();
                            Store::where('id', $order_info['sid'])->inc('total_income', $income)->update();

                            $incomedata['sid'] = $order_info['sid'];
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

                            StoreIncomelog::create($incomedata);
                        }
                    }
                }
            }
        }
    }
}
