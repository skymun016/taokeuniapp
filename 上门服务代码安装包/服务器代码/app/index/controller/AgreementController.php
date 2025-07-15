<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Agreement;

class AgreementController extends Base
{
    public function index()
    {
        $code = input('post.code', '', 'serach_in');
        $data = Agreement::where(['weid' => weid(), 'code' => $code])
            ->order('id desc')
            ->find();
        if (!empty($data)) {
            $data = $data->toArray();
        }

        return $this->json(['data' => $data]);
    }
}
