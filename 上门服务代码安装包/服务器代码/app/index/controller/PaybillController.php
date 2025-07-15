<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Member;
use app\model\MemberCashlogs;

class PaybillController extends Base
{
    public function pay() {

        $money = input('post.money', '', 'serach_in');

        $cashlogsdata['uid'] = UID();
        $cashlogsdata['weid'] = weid();
        $cashlogsdata['order_id'] = '0';
        $cashlogsdata['order_num_alias'] = '0';
        $cashlogsdata['remarks'] = '优惠买单';
        $cashlogsdata['prefix'] = 2;
        $cashlogsdata['amount'] = $money;

        MemberCashlogs::create($cashlogsdata);
        Member::where('id', UID())->dec('balance', $money)->update();

        return $this->json(['data' => $data]);
    }
}
