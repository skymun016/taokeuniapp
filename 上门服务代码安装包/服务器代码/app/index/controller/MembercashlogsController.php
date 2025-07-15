<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\MemberCashlogs;

class MembercashlogsController extends Base
{
    public function list()
    {

        $prefix = input('post.prefix', '', 'serach_in');

        $where['weid'] = weid();
        $where['uid'] = UID();

        if (!empty($prefix)) {
            $where['prefix'] = $prefix;
        }
        $data = MemberCashlogs::where($where)
            ->order('id desc')
            ->select()
            ->toArray();
        return $this->json(['data' => $data]);
    }
}
