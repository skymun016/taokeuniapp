<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Order;
use app\model\OrderStatus;
use app\model\Goods;
use app\model\Member;
use app\model\Technical;
use app\model\Agent;
use app\model\Operatingcity;
use app\model\Tuanzhang;
use app\model\RegisterField;
use app\model\AgentLevel;
use app\model\PartnerLevel;
use app\model\Agreement;
use app\model\OperatingcityLevel;
use app\model\RecoveryWeight;
use app\model\Paymethod;
use app\model\TechnicalLevel;
use app\model\Lang;
use app\model\BottomMenu;
use app\model\GoodsQuantityUnit;

class DashboardController extends Base
{

    function index()
    {
        $weid = weid();
        $where['weid'] = $weid;

        $orderwhere = $where;
        $Goodswhere = $where;
        $Memberwhere = $where;
        $Technicalwhere = $where;

        if (!empty($this->sid)) {
            $sidwhere = $where;
            $sidwhere['sid'] = $this->sid;
            $orderwhere = $sidwhere;
            $Goodswhere = $sidwhere;
            $Memberwhere = $sidwhere;
            $Technicalwhere = $sidwhere;
        } elseif (!empty($this->ocid)) {
            $Operatingcitydata = Operatingcity::find($this->ocid);
            if ($Operatingcitydata) {
                $Operatingcitydata = $Operatingcitydata->toArray();
                if(empty($Operatingcitydata['areatype'])){
					$Operatingcitydata['areatype'] = 3;
				}
                if ($Operatingcitydata['areatype'] == 3) {
                    $orderwhere['shipping_district_name'] = $Operatingcitydata['district_name'];
                    $Goodswhere['district_name'] = $Operatingcitydata['district_name'];
                    $Technicalwhere['district_name'] = $Operatingcitydata['district_name'];
                } elseif ($Operatingcitydata['areatype'] == 2) {
                    $orderwhere['shipping_city_name'] = $Operatingcitydata['city_name'];
                    $Goodswhere['city_name'] = $Operatingcitydata['city_name'];
                    $Technicalwhere['city_name'] = $Operatingcitydata['city_name'];
                } elseif ($Operatingcitydata['areatype'] == 1) {
                    $orderwhere['shipping_province_name'] = $Operatingcitydata['province_name'];
                    $Goodswhere['province_name'] = $Operatingcitydata['province_name'];
                    $Technicalwhere['province_name'] = $Operatingcitydata['province_name'];
                }
            }

            $Memberwhere['ocid'] = $this->ocid;
        } elseif (!empty($this->tzid)) {
            $Tuanzhangdata = Tuanzhang::find($this->tzid);
            if ($Tuanzhangdata) {
                $Tuanzhangdata = $Tuanzhangdata->toArray();
                $orderwhere['shipping_district_name'] = $Tuanzhangdata['district_name'];
                $Goodswhere['district_name'] = $Tuanzhangdata['district_name'];
                $Technicalwhere['district_name'] = $Tuanzhangdata['district_name'];
            }
        }
        $statistical['deliverOrder'] = Order::where($orderwhere)->where('order_status_id', '>', 2)->count();
        $statistical['returnOrder'] = Order::where($orderwhere)->where(['order_status_id' => 6])->count();
        $statistical['sellOut'] = Goods::where($Goodswhere)->where(['quantity' => 0])->count();
        $statistical['member_count'] = Member::where($Memberwhere)->count();
        $statistical['technical_count'] = Technical::where($Technicalwhere)->count();
        $statistical['agent_count'] = Agent::where($where)->count();
        $statistical['order_count'] = $this->getOrdercount();
        $statistical['order_paycount'] = $this->getOrdercount(['where' => ['order_status_id' => 2]]);
        $statistical['order_paytotal'] = Order::where($orderwhere)->where('pay_time', '>', 0)->sum('total') + Order::where($orderwhere)->where('pay_time', '>', 0)->sum('additional');
        $statistical['order_paytotal'] = round($statistical['order_paytotal'], 2);
        $statistical['order_pay'] = $this->getOrdercount(['pay' => 1]);
        $statistical['memberAccess'] = $this->getAccess();

        $statistical['accessTopay_conversionrate'] = to_percent($statistical['order_paycount'], $statistical['memberAccess']);
        $statistical['accessToorder_conversionrate'] = to_percent($statistical['order_count'], $statistical['memberAccess']);
        $statistical['orderTopay_conversionrate'] = to_percent($statistical['order_paycount'], $statistical['order_count']);

        $clinchadeal[] = $this->getOrderdaycount(['getday' => 7, 'pay' => 1]);
        $clinchadeal[] = $this->getOrderdaycount(['getday' => 6, 'pay' => 1]);
        $clinchadeal[] = $this->getOrderdaycount(['getday' => 5, 'pay' => 1]);
        $clinchadeal[] = $this->getOrderdaycount(['getday' => 4, 'pay' => 1]);
        $clinchadeal[] = $this->getOrderdaycount(['getday' => 3, 'pay' => 1]);
        $clinchadeal[] = $this->getOrderdaycount(['getday' => 2, 'pay' => 1]);
        $clinchadeal[] = $this->getOrderdaycount(['getday' => 1, 'pay' => 1]);

        $res['statistical'] = $statistical;
        $res['clinchadeal'] = $clinchadeal;
        $res['goodssaletop5'] = Goods::where($Goodswhere)->order('sale_count desc')->limit(5)->select()->toArray();
        $res['consumedop5'] = Member::where($Memberwhere)->order('totleconsumed desc')->limit(5)->select()->toArray();
        $res['service_timestop5'] = Technical::where($Technicalwhere)->order('service_times desc')->limit(5)->select()->toArray();

        if (config('database.app_name') == ('xm_mal' . 'lv3')) {
            $res['version'] = 'v3';
        } else {
            $res['version'] = 'v2';
        }

        $data['data'] = $res;


        return $this->json($data);
    }
    function datainitial()
    {
        RegisterField::datainitial('member');
        RegisterField::datainitial('agent');
        RegisterField::datainitial('partner');
        RegisterField::datainitial('technical');
        RegisterField::datainitial('store');
        RegisterField::datainitial('operatingcity');

        AgentLevel::datainitial();
        PartnerLevel::datainitial();
        Agreement::datainitial();
        OperatingcityLevel::datainitial();
        Paymethod::datainitial();
        TechnicalLevel::datainitial();
        RecoveryWeight::datainitial();
        Lang::datainitial();

        BottomMenu::datainitial('bottom');
        BottomMenu::datainitial('technical');
        BottomMenu::datainitial('store');
        BottomMenu::datainitial('member');

        OrderStatus::datainitial('goods');
        OrderStatus::datainitial('goodsuser');
        OrderStatus::datainitial('service');
        OrderStatus::datainitial('storeservice');
        OrderStatus::datainitial('serviceuser');

        GoodsQuantityUnit::datainitial(1);
        GoodsQuantityUnit::datainitial(2);

        return $this->json($data);
    }

    public function getAccess()
    {

        $getdata = input('get.');
        if (empty($getdata['getday'])) {
            $getdata['getday'] = 1;
        }
        $query = Member::where(['weid' => weid()]);

        if (empty($getdata['date_start'])) {
            $getdata['date_start'] = date("Y-m-d H:i:s", strtotime("-" . $getdata['getday'] . " day"));
        }

        if (empty($getdata['date_end'])) {
            $getdata['date_end'] = date("Y-m-d H:i:s");
        }

        if (!empty($getdata['date_start'])) {
            $query->where('regdate', '>=', strtotime(trim($getdata['date_start'])));
        }
        if (!empty($getdata['date_end'])) {
            $query->where('regdate', '<=', strtotime(trim($getdata['date_end'])) + 86400);
        }

        return $query->count();
    }
    public function getOrdercount($data = [])
    {
        $where = $data['where'];
        $pay = $data['pay'];
        $getdata = input('get.');
        if (empty($getdata['getday'])) {
            $getdata['getday'] = 1;
        }
        $where['weid'] = weid();

        if (!empty($this->sid)) {
            $where['sid'] = $this->sid;
        } elseif (!empty($this->ocid)) {

            $Operatingcitydata = Operatingcity::find($this->ocid);
            if ($Operatingcitydata) {
                $Operatingcitydata = $Operatingcitydata->toArray();
                if(empty($Operatingcitydata['areatype'])){
					$Operatingcitydata['areatype'] = 3;
				}
                if ($Operatingcitydata['areatype'] == 3) {
                    $where['shipping_district_name'] = $Operatingcitydata['district_name'];
                } elseif ($Operatingcitydata['areatype'] == 2) {
                    $where['shipping_city_name'] = $Operatingcitydata['city_name'];
                } elseif ($Operatingcitydata['areatype'] == 1) {
                    $where['shipping_province_name'] = $Operatingcitydata['province_name'];
                }
            }
        } elseif (!empty($this->tzid)) {

            $Tuanzhangdata = Tuanzhang::find($this->tzid);
            if ($Tuanzhangdata) {
                $Tuanzhangdata = $Tuanzhangdata->toArray();
                $where['shipping_district_name'] = $Tuanzhangdata['district_name'];
            }
        }
        $query =  Order::where($where);

        if (empty($getdata['date_start'])) {
            $getdata['date_start'] = date("Y-m-d H:i:s", strtotime("-" . $getdata['getday'] . " day"));
        }

        if (empty($getdata['date_end'])) {
            $getdata['date_end'] = date("Y-m-d H:i:s");
        }

        if (!empty($getdata['date_start'])) {
            $query->where('create_time', '>=', strtotime(trim($getdata['date_start'])));
        }
        if (!empty($getdata['date_end'])) {
            $query->where('create_time', '<=', strtotime(trim($getdata['date_end'])) + 86400);
        }

        if (empty($pay)) {
            return $query->count();
        } else {
            return $query->sum('total');
        }
    }

    public function getOrderdaycount($data = [])
    {
        $where = $data['where'];
        $pay = $data['pay'];
        $getdata['getday'] = $data['getday'];
        $where['weid'] = weid();
        if (!empty($this->sid)) {
            $where['sid'] = $this->sid;
        } elseif (!empty($this->ocid)) {

            $Operatingcitydata = Operatingcity::find($this->ocid);
            if ($Operatingcitydata) {
                $Operatingcitydata = $Operatingcitydata->toArray();
                if(empty($Operatingcitydata['areatype'])){
					$Operatingcitydata['areatype'] = 3;
				}
                if ($Operatingcitydata['areatype'] == 3) {
                    $where['shipping_district_name'] = $Operatingcitydata['district_name'];
                } elseif ($Operatingcitydata['areatype'] == 2) {
                    $where['shipping_city_name'] = $Operatingcitydata['city_name'];
                } elseif ($Operatingcitydata['areatype'] == 1) {
                    $where['shipping_province_name'] = $Operatingcitydata['province_name'];
                }
            }
        } elseif (!empty($this->tzid)) {

            $Tuanzhangdata = Tuanzhang::find($this->tzid);
            if ($Tuanzhangdata) {
                $Tuanzhangdata = $Tuanzhangdata->toArray();
                $where['shipping_district_name'] = $Tuanzhangdata['district_name'];
            }
        }

        $query =  Order::where($where);

        if (empty($getdata['date_start'])) {
            $getdata['date_start'] = date("Y-m-d H:i:s", strtotime("-" . $getdata['getday'] . " day"));
        }

        if (empty($getdata['date_end'])) {
            $getdata['date_end'] = date("Y-m-d H:i:s", strtotime("-" . ($getdata['getday'] - 1) . " day"));
        }

        if (!empty($getdata['date_start'])) {
            $query->where('create_time', '>=', strtotime(trim($getdata['date_start'])));
        }
        if (!empty($getdata['date_end'])) {
            $query->where('create_time', '<=', strtotime(trim($getdata['date_end'])) + 86400);
        }

        if (empty($pay)) {
            return $query->count();
        } else {
            //var_dump($query->getLastsql());
            return $query->sum('total');
        }
    }
}
