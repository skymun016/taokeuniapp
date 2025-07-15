<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\MemberAuthGroup;

class MemberauthgroupController extends Base
{

    public function list()
    {
        $weid = weid();
        $data = MemberAuthGroup::where(['status' => 1, 'weid' => $weid])
            ->order('id asc')
            ->select()
            ->toArray();

        return $this->json(['data' => $data]);
    }
}
