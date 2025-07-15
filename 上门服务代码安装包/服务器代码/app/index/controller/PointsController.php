<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Points;

class PointsController extends Base
{
    //积分明细
    public function list()
    {
        $where['weid'] = weid();
        $where['uid'] = UID();
        $log = Points::where($where)->select()->toArray();

        foreach ($log as &$vo) {
            if ($vo['type'] == 1) {
                $vo['typeStr'] = '下单';
            } elseif ($vo['type'] == 2) {
                $vo['typeStr'] = '管理员操作';
            }
        }
        $data = $log;
        return $this->json(['data' => $data]);
    }
}
