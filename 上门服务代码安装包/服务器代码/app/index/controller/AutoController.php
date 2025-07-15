<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Config;

class AutoController extends Base
{

    public function index()
    {
        $weid = weid();
        

        return $this->json(['data' => time_format(time())]);
    }
}
