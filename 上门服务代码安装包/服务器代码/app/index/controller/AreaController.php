<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Area;

class AreaController extends Base
{

    public function tree()
    {
        $data = Area::getTree();
        return $this->json(['data' => $data]);
    }
    public function abc()
    {
        $data[0]['letter'] = 'A';
        $data[0]['data'] = [];

        $ii = 1;
        for ($i = 65; $i < 91; $i++) {
            $abc = strtoupper(chr($i));
            $res = Area::where(['letter' => $abc, 'area_deep' => 2])->select()->toArray();

            if (!empty($res)) {
                $data[$ii]['letter'] = $abc;
                foreach ($res as $key => $vo) {
                    $data[$ii]['data'][$key]['cityName'] = $vo['area_name'];
                    $data[$ii]['data'][$key]['keyword'] = $vo['keyword'];
                }
                $ii++;
            }
        }
        return $this->json(['data' => $data]);
    }
    public function province()
    {
        $province = Area::get_province();
        foreach ($province as $key => $vo) {
            $data[$key]['id'] = $vo['id'];
            $data[$key]['level'] = $vo['area_deep'];
            $data[$key]['name'] = $vo['area_name'];
        }

        return $this->json(['data' => $data]);
    }

    public function child()
    {
        $errno = 0;
        $message = '返回消息';
        $pid = (int) input('post.pid', '', 'intval');

        $child = Area::get_child($pid);
        if (!empty($child)) {
            foreach ($child as $key => $vo) {
                $data[$key]['id'] = $vo['id'];
                $data[$key]['pid'] = $vo['area_parent_id'];
                $data[$key]['level'] = $vo['area_deep'];
                $data[$key]['name'] = $vo['area_name'];
            }
        }

        return $this->json(['data' => $data]);
    }
}
