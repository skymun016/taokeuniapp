<?php

namespace app\model;

use think\Model;

class Tuanzhang extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'tuanzhang';

    public static function getTitle($id)
    {
        $data = self::find($id);
        return $data->title;
    }
    public static function getTitlebyuuid($uuid)
    {
        $data = self::where('uuid', $uuid)->find();
        return $data->title;
    }

    public static function getTitlebyuid($uid)
    {
        $data = self::where('uid', $uid)->find();
        return $data->title;
    }
    public static function getcityName($uuid)
    {
        $data = self::where('uuid', $uuid)->find();
        return $data->city_name;
    }
    public static function getTel($uuid)
    {
        $data = self::where('uuid', $uuid)->find();
        return $data->tel;
    }

    public static function conversion($vo)
    {
        $vo['cate_ids'] = Category::getmultiple($vo['cate_ids']) ?? '无';
        $vo['level'] = TuanzhangLevel::getTitle($vo['level']) ?? '初级';

        if (!empty($vo['uuid'])) {
            $vo['username'] = Users::getusername($vo['uuid']);
        }
        $vo['region_name'] = $vo['province_name'] . $vo['city_name'] . $vo['district_name'];

        $vo = RegisterField::conversion($vo);
        return $vo;
    }

    public static function getInfo($uuid)
    {
        $data = self::where('uuid', $uuid)->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }
        return $data;
    }

    public static function getInfobyid($id)
    {
        $data = self::find($id);
        if (!empty($data)) {
            $data = $data->toArray();
        } else {
            $data = [];
        }
        return $data;
    }

    public static function getInfobyuid($uid)
    {
        $uuid = UuidRelation::getuuid($uid, 'tuanzhang');
        if (!empty($uuid)) {
            $data = Tuanzhang::where(['uuid' => $uuid])->find();
            if (!empty($data)) {
                $data = $data->toArray();
            }
        }
        return $data;
    }

    public static function getpcarray()
    {
        $data = self::field('uuid,title')->where(['weid' => weid(), 'status' => 1])->select()->toArray();
        $datalist = [];
        foreach ($data as $key => $vo) {
            if (!empty($vo['uuid'])) {
                $datalist[$key]['val'] = $vo['uuid'];
                $datalist[$key]['key'] = $vo['title'];
            }
        }
        return $datalist;
    }
    public static function gelist_bycity($city_name)
    {
        $where['weid'] = weid();
        $where['status'] = 1;
        $where['city_name'] = $city_name;

        $query = Tuanzhang::where($where);

        $Sort = 'id desc';

        $data = $query->order($Sort)->select()->toArray();

        return $data;
    }

    public static function setIncome($order_info)
    {
        $uuid =  OrderTuanzhang::getuuid($order_info['id']);
        if (!empty($uuid)) {
            $Tuanzhang = Tuanzhang::where(['uuid' => $uuid])->find();
            if (!empty($Tuanzhang)) {
                $Tuanzhang = $Tuanzhang->toArray();
            }

            if (!empty($Tuanzhang['status'])) {
                $percent = TuanzhangLevel::getPercent($Tuanzhang['level']);
                if (empty($percent)) {
                    $percent = Config::getconfig('tuanzhang')['return_percent'];
                }

                if ($percent > 0) {

                    if (empty($order_info['cat_id'])) {
                        $income = OrderGoods::getCommission($order_info, 'tuanzhang', $percent);
                    } else {
                        $income = $order_info['total'];
                        $income = ($income * percent_to_num($percent));
                    }

                    $return_percent = $percent;
                    if ($income > 0) {

                        if ($income > 0 && $income < 0.01) {
                            $income = 0.01;
                        }

                        $TuanzhangIncomelog = TuanzhangIncomelog::where([
                            'uuid' => $uuid,
                            'weid' => $order_info['weid'],
                            'order_id' =>  $order_info['id'],
                        ])->find();

                        if (empty($TuanzhangIncomelog)) {

                            Tuanzhang::where('uuid', $uuid)->inc('income', $income)->update();
                            Tuanzhang::where('uuid', $uuid)->inc('total_income', $income)->update();
                            Tuanzhang::where('uuid', $uuid)->inc('service_times', 1)->update();

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

                            TuanzhangIncomelog::create($incomedata);
                        }
                    }
                }
            }
        }
    }
}
