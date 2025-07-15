<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Viporder;
use app\model\Member;
use app\model\MemberAuthGroup;

class ViporderController extends Base
{
    public function create()
    {
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();
        $gid = (int) input('post.gid', '', 'serach_in');

        $AuthGroup = MemberAuthGroup::find($gid);

        if (!empty($AuthGroup)) {

            $postdata['weid'] = weid();
            $postdata['uid'] = $memberinfo['id'];
            $postdata['gid'] = $gid;
            $postdata['order_num_alias'] = build_order_no();
            $postdata['pay_subject'] = $AuthGroup->title;
            $postdata['total'] = $AuthGroup->upgrademoney;

            $data = Viporder::create($postdata);
        }

        if (!empty($data)) {
            $data = $data->toArray();
            $errno = 0;
        }
        return $this->json(['errno' => $errno, 'data' => $data]);
    }
}
