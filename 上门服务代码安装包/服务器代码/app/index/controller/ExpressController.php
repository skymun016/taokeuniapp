<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Express;

class ExpressController extends Base
{

    public function index()
    {
        $data = Express::select()
            ->order('sort asc')
            ->toArray();
        return $this->json(['data' => $data]);
    }
}
