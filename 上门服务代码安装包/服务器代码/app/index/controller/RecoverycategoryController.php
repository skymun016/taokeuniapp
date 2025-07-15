<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\RecoveryCategory;

class RecoverycategoryController extends Base
{
    public function all()
    {
        $data = RecoveryCategory::getcatapiall(0);
        return $this->json(['data' => $data]);
    }
}
