<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Paymethod;


class PaymethodController extends Base
{
    //支付方式列表
    public function list()
    {

        $data = Paymethod::where(['weid' => weid(), 'status' => 1])
            ->order('sort asc')
            ->select()
            ->toArray();
        $data[0]['default'] = $data[0]['default']['code'];
        foreach ($data as $i => $vo) {
            if (empty($vo['code'])) {
                unset($data[$i]);
            }
            if (($vo['code'] == 'wx_pay')) {
                $data[0]['default'] = $vo['code'];
            }
        }
        if (empty($data)) {
            Paymethod::update(['code' => 'wx_pay', 'weid' => 0]);
            $data  = Paymethod::where(['weid' => 0])->order('sort asc')->select()->toArray();
        }
        if (empty($data[0]['default'])) {
            $data[0]['default'] = $data[0]['code'];
        }
        return $this->json(['data' => $data]);
    }

    function getInfo()
    {
        $code = input('post.code', '', 'serach_in');

        if ($code) {
            $data = Paymethod::where(['code' => $code, 'weid' => weid()])->find();

            if ($data) {
                $data = $data->toArray();
                $data['settings'] = iunserializer($data['settings']);
            }

            return $this->json(['data' => $data]);
        }
    }
}
