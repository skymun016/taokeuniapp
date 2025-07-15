<?php

namespace app\model;

use think\Model;

class Hospital extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'hospital';

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
    public static function getTitlebyuid($uuid = '')
    {
        $mo = self::where('uuid', $uuid)->find();
        return $mo->title;
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
        if (empty($vo['cid'])) {
            $vo['cid'] = '未设置';
        } else {
            $vo['cid'] = HospitalCate::getTitle($vo['cid']);
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
        return $vo;
    }
    public static function setIncome($order_info)
    {
        if (empty($order_info['hid'])) {
            $uid =  OrderStaff::getuuid($order_info['id']);
            if (!empty($uuid)) {
                $data = Technical::where('uuid', $uuid)->find();
                if (!empty($data)) {
                    $order_info['hid'] = $data->hid;
                }
            }
        }

        if (!empty($order_info['hid'])) {
            $Hospital = Hospital::find($order_info['hid']);
            if (!empty($Hospital)) {
                $Hospital = $Hospital->toArray();
            }

            if (!empty($Hospital['status'])) {
                $percent = HospitalCate::getPercent($Hospital['cid']);

                if (empty($percent)) {
                    $percent = Config::getconfig('store')['return_percent'];
                }

                if ((int)$percent > 0) {

                    $income = ($order_info['total'] * percent_to_num($percent));

                    $return_percent = $percent;

                    if ($income > 0 && $income < 0.01) {
                        $income = 0.01;
                    }

                    Hospital::where('id', $order_info['hid'])->inc('income', $income)->update();
                    Hospital::where('id', $order_info['hid'])->inc('total_income', $income)->update();

                    $incomedata['hid'] = $order_info['hid'];
                    $incomedata['ptype'] = 1;
                    $incomedata['weid'] = weid();
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
