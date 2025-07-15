<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Incomelog;
use app\model\Member;

class IncomelogController extends Base
{
    public function index()
    {
        $uid = UID();
        $ptype = input('get.ptype', '', 'serach_in');

        $where['weid'] = weid();
        $where['uid'] = $uid;
        $where['ptype'] = $ptype;

        $data = Incomelog::where($where)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($data as &$vo) {
            $vo['username'] = Member::get_name($vo['buyer_id']);
            $vo['pay_time'] = time_format($vo['pay_time']);
        }
        return $this->json(['data' => $data]);
    }
}
