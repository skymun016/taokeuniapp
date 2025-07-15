<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Article;
use app\model\ArticleCategory;

class ArticleController extends Base
{

    public function list()
    {
        $cid = input('post.cid', '', 'serach_in');
        $where['weid'] = weid();
        if (!empty($cid)) {
            $where['cid'] = $cid;
        }

        $data = Article::where($where)
            ->order('id desc')
            ->select()
            ->toArray();

        return $this->json(['data' => $data]);
    }

    public function detail()
    {
        $id = input('get.id', '', 'intval');
        if (!empty($id)) {
            $where['weid'] = weid();
            $where['id'] = $id;
            $data = Article::where($where)->find();
            if (!empty($data)) {
                $data = $data->toArray();
                $data['content'] = \app\model\DomainReplace::setreplace($data['content']);
                $data['create_time'] =  time_ymd($data['create_time']);
                $data['cate'] =  ArticleCategory::getTitle($data['cid']);
                
            }
        }
        return $this->json(['data' => $data]);
    }
}
