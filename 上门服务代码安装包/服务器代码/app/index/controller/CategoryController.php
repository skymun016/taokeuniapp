<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Category;
use app\model\Goods;
use app\model\Config;

class CategoryController extends Base
{
    public function list()
    {
        $config = Config::getconfig();
        if (!\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $ptype = input('get.ptype', '', 'serach_in');
            $data = Category::getlist(['pid' => 0, 'status' => 1, 'ptype' => $ptype]);
            foreach ($data as &$vo) {
                $vo['icon'] = toimg($vo['image']);
                $vo['name'] = $vo['title'];
            }
        }
        return $this->json(['data' => $data]);
    }

    public function all()
    {
        $config = Config::getconfig();
        if (!\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $ptype = input('get.ptype', '', 'serach_in');
            $config = Config::getconfig('pagestyle');

            if (empty($config['categorystyle'])) {
                $config['categorystyle'] = 1;
            }
            if ($config['categorystyle'] == 1) {
                $data = Category::getlist(['pid' => 0, 'status' => 1, 'ptype' => $ptype]);
            } elseif ($config['categorystyle'] == 2) {
                $data = Category::getcatapiall(0, $ptype);
            } elseif ($config['categorystyle'] == 3) {
                $data = Category::getlist(['pid' => 0, 'status' => 1, 'ptype' => $ptype]);
                foreach ($data as &$vo) {
                    $vo['image'] = toimg($vo['image']);
                    $vo['goodslist'] = Goods::getGoodsBycat(['cat' => $vo['id'], 'count' => 100]);
                }
            }
        }
        return $this->json(['data' => $data]);
    }
    public function demand()
    {
        $config = Config::getconfig();
        if (!\app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'))) {
            $data = Category::getcatapiall(0, 2);
        }
        return $this->json(['data' => $data]);
    }
}
