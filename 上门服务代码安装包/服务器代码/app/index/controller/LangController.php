<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Lang;

class LangController extends Base
{

    //2023年9月30废弃
    public function index()
    {
        $data = Lang::getLang();
        return $this->json(['data' => $data]);
    }
    public function getlang()
    {
        $data = Lang::getLang();
        return $this->json(['data' => $data]);
    }
}
