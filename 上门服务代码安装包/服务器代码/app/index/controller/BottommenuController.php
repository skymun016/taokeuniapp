<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\BottomMenu;

class BottommenuController extends Base
{

    public function list()
    {
        $data = BottomMenu::getBottommenu($this->userInfo);
        return $this->json(['data' => $data]);
    }
    public function getindex()
    {
        $where['weid'] = weid();
        $where['is_index'] = 1;
        $where['tid'] = 0;
        $where['module'] = 'bottom';

        $data = BottomMenu::where($where)
            ->order('sort asc')
            ->find();
        if (empty($data)) {
            $data['url'] = '/pages/index/index';
        } else {
            $data = $data->toArray();
        }

        return $this->json(['data' => $data]);
    }
}
