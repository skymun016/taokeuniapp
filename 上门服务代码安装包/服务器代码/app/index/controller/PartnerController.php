<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Member;
use app\model\Config;
use app\model\Partner;

class PartnerController extends Base
{
    public function amount()
    {
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();
        $data['base'] = $memberinfo;

        return $this->json(['data' => $data]);
    }

    public function check()
    {
        $uid = UID();
        $data = Partner::where(['uid' => $uid, 'status' => 1])->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }

        $data['id'] = $data['id'];
        $data['is_submitaudit'] = \app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'));

        return $this->json(['msg' => '您还不是合伙人', 'data' => $data]);
    }
}
