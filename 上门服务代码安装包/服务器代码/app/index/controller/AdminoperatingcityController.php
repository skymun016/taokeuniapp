<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Operatingcity;
use app\model\Openid;
use app\model\UuidRelation;

class AdminoperatingcityController extends Base
{

    public function detail()
    {
        $message = '';
        $is_login = 0;

        $data = Operatingcity::getInfobyuid(UID());
        
        if ($data['status'] != 1) {
            $data = 0;
            $message = '请先登录！';
        } else {
            $is_login = 1;
            if ($data['total_income'] == null) {
                $data['total_income'] = 0;
            }
            if ($data['income'] == null) {
                $data['income'] = 0;
            }
        }

        return $this->json(['message' => $message, 'is_login' => $is_login, 'data' => $data]);
    }

    public function check()
    {
        $uuid = UuidRelation::getuuid(UID(), 'operatingcity');
        $data = Operatingcity::where(['uuid' => $uuid])->find();
        if ($data) {
            $data = $data->toArray();
        }
        return $this->json(['data' => $data]);
    }
}
