<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\ArticleCategory;


class ArticlecategoryController extends Base
{

    public function list()
    {

            $where['weid'] = weid();
            $data = ArticleCategory::where($where)
                ->order('sort asc')
                ->select()
                ->toArray();

            foreach ($data as &$vo) {
                $vo['icon'] = toimg($vo['image']);
            }
        
        return $this->json(['data' => $data]);
    }
}
