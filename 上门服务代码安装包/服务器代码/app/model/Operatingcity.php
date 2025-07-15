<?php

namespace app\model;

use think\Model;

class Operatingcity extends Model
{
    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'operatingcity';

    public static function getTitle($id = '')
    {
        $mo = self::find($id);
        return $mo->title;
    }

    public static function getInfobyuid($uid)
    {
        $uuid = UuidRelation::getuuid($uid, 'operatingcity');
        if (!empty($uuid)) {
            $data = self::where(['uuid' => $uuid])->find();
            if (!empty($data)) {
                $data = $data->toArray();
            }
        }
        return $data;
    }

    public static function conversion($vo)
    {
        if ($vo['end_time'] == 0) {
            $vo['end_time'] = '永久有效';
        } else {
            $vo['end_time'] = time_format($vo['end_time']);
        }
        $vo['cate_ids'] = Category::getmultiple($vo['cate_ids']) ?? '无';
        $vo['level'] = OperatingcityLevel::getTitle($vo['level']) ?? '初级';
        $vo['areatype'] = OperatingcityType::getTitle($vo['areatype']) ?? '未设置';

        if (!empty($vo['uuid'])) {
            $vo['username'] = Users::getusername($vo['uuid']);
        }
        $vo['region_name'] = $vo['province_name'] . $vo['city_name'] . $vo['district_name'];
        $vo = RegisterField::conversion($vo);
        return $vo;
    }

    public static function getCityid($id = '')
    {
        $mo = self::find($id);
        return $mo->city_id;
    }

    public static function getCityname($id = '')
    {
        $mo = self::find($id);
        return $mo->city_name;
    }


    public static function setIncome($order_info)
    {
        if (!empty($order_info['shipping_province_name'])) {
            $Operatingcity1 = self::where([
                'areatype' => 1,
                'status' => 1,
                'province_name' => $order_info['shipping_province_name']
            ])->order('id asc')->select()->toArray();

            if (!empty($Operatingcity1)) {
                foreach ($Operatingcity1 as $ov1) {
                    self::calculate($order_info, $ov1);
                }
            }
        }

        if (!empty($order_info['shipping_city_name'])) {
            $Operatingcity2 = self::where([
                'areatype' => 2,
                'status' => 1,
                'city_name' => $order_info['shipping_city_name']
            ])->order('id asc')->select()->toArray();

            if (!empty($Operatingcity2)) {
                foreach ($Operatingcity2 as $ov2) {
                    self::calculate($order_info, $ov2);
                }
            }
        }

        if (!empty($order_info['shipping_district_name'])) {
            $Operatingcity3 = self::where([
                'areatype' => 3,
                'status' => 1,
                'district_name' => $order_info['shipping_district_name']
            ])->order('id asc')->select()->toArray();

            if (!empty($Operatingcity3)) {
                foreach ($Operatingcity3 as $ov3) {
                    self::calculate($order_info, $ov3);
                }
            }
        }
    }

    public static function calculate($order_info, $Operatingcity)
    {

        if (!empty($Operatingcity['status'])) {
            $roletype = 'district';
            if ($Operatingcity['areatype'] == 1) {
                $roletype = 'province';
            }
            if ($Operatingcity['areatype'] == 2) {
                $roletype = 'city';
            }
            $percent =  OperatingcityLevel::getPercent($Operatingcity['level']);

            if (empty($percent)) {
                $percent =  Config::getconfig('operatingcity')['return_percent'];
            }

            if ($percent > 0) {

                if (empty($order_info['cat_id'])) {
                    $income = OrderGoods::getCommission($order_info, $roletype, $percent);
                } else {
                    $income = $order_info['total'];
                    $income = ($income * percent_to_num($percent));
                }


                $return_percent = $percent;

                if ($income > 0) {
                    if ($income > 0 && $income < 0.01) {
                        $income = 0.01;
                    }

                    $OperatingcityIncomelog = OperatingcityIncomelog::where([
                        'ocid' => $Operatingcity['id'],
                        'areatype' => $Operatingcity['areatype'],
                        'weid' => $order_info['weid'],
                        'order_id' =>  $order_info['id'],
                    ])->find();

                    if (empty($OperatingcityIncomelog)) {
                        Operatingcity::where('id', $Operatingcity['id'])->inc('income', $income)->update();
                        Operatingcity::where('id', $Operatingcity['id'])->inc('total_income', $income)->update();
                        $incomedata['ocid'] = $Operatingcity['id'];
                        $incomedata['ptype'] = 1;
                        $incomedata['areatype'] = $Operatingcity['areatype'];
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
                        OperatingcityIncomelog::create($incomedata);
                    }
                }
            }
        }
    }

    public static function getsettings($id)
    {
        $res = Operatingcity::find($id);
        if ($res) {
            $res = $res->toArray();
            $settings = unserialize($res['settings']);
        } else {
            $settings = [];
        }
        return $settings;
    }
}
