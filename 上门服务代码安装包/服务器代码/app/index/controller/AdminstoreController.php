<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Store;
use app\model\StoreImage;
use app\model\UuidRelation;
use app\model\Category;

class AdminstoreController extends Base
{

    public function detail()
    {
        $message = '';
        $is_login = 0;
        
        $data = Store::getInfobyuid(UID());
        if (!empty($data)) {
            if ($data['status'] == 1) {
                $is_login = 1;
                $data['store_logo'] = toimg($data['store_logo']);
                $data['store_banner'] = StoreImage::field('image')
                    ->where(['sid' => $data['id'], 'ptype' => 'banner'])
                    ->order('sort asc')
                    ->select()
                    ->toArray();

                $data['content_img'] = StoreImage::field('image')
                    ->where(['sid' => $data['id'], 'ptype' => 'content'])
                    ->order('sort asc')
                    ->select()
                    ->toArray();
            } else {
                $data = 0;
                $message = '请先登录！';
            }
        }

        return $this->json(['message' => $message, 'is_login' => $is_login, 'data' => $data]);
    }

    public function check()
    {
        $data = Store::getInfobyuid(UID());
        $data['Category'] = Category::gettoparray();

        return $this->json(['data' => $data]);
    }
}
