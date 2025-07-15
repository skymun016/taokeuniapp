<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Config;

class SubscribemessageController extends Base
{
    public function complete()
    {

        $res = Config::getconfig('subscribemessage');
        $data[] = $res['complete_tpl'];

        return $this->json(['data' => $data]);
    }
}
