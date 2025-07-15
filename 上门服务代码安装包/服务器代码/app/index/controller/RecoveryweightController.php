<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\RecoveryWeight;

class RecoveryweightController extends Base
{
    public function list()
    {
        $weid = weid();
        $query = RecoveryWeight::where(['weid' => $weid]);

        $data = $query->order('sort asc,id asc')->select()->toArray();

        foreach ($data as &$vo) {
            if ($vo['begin_weight'] == '0') {
                $vo['title'] = $vo['end_weight'] . '公斤以下';
            } else if ($vo['end_weight'] == '0') {
                $vo['title'] = $vo['begin_weight'] . '公斤以上';
            } else {
                $vo['title'] = $vo['begin_weight'] . '-' . $vo['end_weight'];
            }
        }

        return $this->json(['data' => $data]);
    }
}
