<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\ServiceTime;
use app\model\OrderStaff;
use app\model\GoodsBuynowinfo;

class ServicetimeController extends Base
{

    public function index()
    {
        $technicalId = input('post.technicalId', '', 'serach_in');
        $buynowinfoid = input('post.buynowinfoid', '', 'serach_in');
        $selectDate = input('post.selectDate', '', 'serach_in');
        if (empty($selectDate)) {
            $selectDate = date("Y-m-d");
        }

        $buynowinfo = GoodsBuynowinfo::where(['id' => $buynowinfoid])->find();

        if (!empty($buynowinfo)) {
            $buynowinfo = $buynowinfo->toArray();
            $buynowinfodata = iunserializer($buynowinfo['data']);;
        }

        $data = ServiceTime::where(['weid' => weid(),'ptype'=>(int)$buynowinfodata['shopList']['category']['servicetime_ptype']])
            ->order('id asc,sort asc')
            ->select()
            ->toArray();

        foreach ($data as &$vo) {
            $vo['time'] = $vo['begin_time'];
            $vo['seltime'] = $vo['begin_time'] . '-' . $vo['end_time'];

            if (!empty($technicalId)) {
                if (OrderStaff::checkstaff($technicalId, $selectDate, $vo['begin_time'], $vo['end_time'])) {
                    $vo['disable'] = true;
                }
            }
        }
        return $this->json(['data' => $data]);
    }
}
