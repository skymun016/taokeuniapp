<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Order;
use app\model\OrderGoods;
use app\model\OrderStaff;
use app\model\Category;
use app\model\Member;
use app\model\Comment;
use app\model\Technical;

class CommentController extends Base
{
    public function add()
    {
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();

        $postJsonString = input('post.postJsonString', '', 'serach_in');
        $postdata = json_decode($postJsonString, true);

        $technical_uuid = OrderStaff::getuuid($postdata['orderInfo']['id']);

        if (empty($postdata['orderInfo']['cat_id'])) {
            foreach ($postdata['goods'] as $vo) {
                Comment::create([
                    'weid' => weid(),
                    'uid' => $memberinfo['id'],
                    'nick_name' => $memberinfo['nickname'],
                    'head_img_url' => $memberinfo['userpic'],
                    'status' => 0,
                    'technical_uuid' => $technical_uuid,
                    'order_id' => $vo['order_id'],
                    'goods_id' => $vo['goods_id'],
                    'level' => $vo['level'],
                    'content' => $vo['content']
                ]);
            }
        } else {
            Comment::create([
                'weid' => weid(),
                'uid' => $memberinfo['id'],
                'nick_name' => $memberinfo['nickname'],
                'head_img_url' => $memberinfo['userpic'],
                'status' => 0,
                'order_id' => $postdata['orderInfo']['id'],
                'technical_uuid' => $technical_uuid,
                'cat_id' => $postdata['orderInfo']['cat_id'],
                'level' => $postdata['cateComment']['level'],
                'content' => $postdata['cateComment']['content']
            ]);
        }

        Order::update(['is_comment' => 1, 'id' => $postdata['orderInfo']['id']]);
        //增加评价量
        if (!empty($technical_uuid)) {
            Technical::where('uuid', $technical_uuid)->inc('comment')->update();
        }

        return $this->json(['msg' => '感谢您的评价', 'data' => $data]);
    }

    public function getorder()
    {
        $id = input('get.id', '', 'intval');
        $data =   Order::order_info($id);

        if (!empty($data['orderInfo']['cat_id'])) {
            $data['cateComment']['level'] = 5;
            $data['cateComment']['content'] = '';
            $data['orderInfo']['cateMap']['image'] = Category::getImage($data['orderInfo']['cat_id']);
        }

        if ($data['orderInfo']['ptype'] == 2) {
            $data['technical'] = OrderStaff::getTechnical($data['orderInfo']['id']);
            $data['technical']['touxiang'] = toimg($data['technical']['touxiang']);
        }
        foreach ($data['goods'] as &$vo) {
            $vo['goods']['image'] = toimg($vo['goods']['image']);
            $vo['level'] = 5;
            $vo['content'] = '';
        }


        return $this->json(['data' => $data]);
    }

    public function gettechnicalcomment()
    {

        $uuid = input('get.uuid', '', 'serach_in');
        $where['technical_uuid'] = $uuid;
        $where['status'] = 1;
        $data = Comment::where($where)->order('id asc')->select()->toArray();
        foreach ($data as &$vo) {
            $vo['nick_name'] = Author()::substr_cut($vo['nick_name']);
        }
        return $this->json(['data' => $data]);
    }

    public function getgoodscomment()
    {

        $goods_id = input('get.goodsId', '', 'serach_in');
        $where['goods_id'] = $goods_id;
        $where['status'] = 1;
        $data =    Comment::where($where)->order('id asc')->select()->toArray();
        foreach ($data as &$vo) {
            $vo['nick_name'] = Author()::substr_cut($vo['nick_name']);
        }
        return $this->json(['data' => $data]);
    }

    public function getgoods()
    {

        $goods_id = input('get.goodsId', '', 'serach_in');
        $OrderGoods = new OrderGoods;

        $data =   $OrderGoods->getOrderGoods($goods_id);

        foreach ($data as &$vo) {
            $vo['level'] = 5;
            $vo['content'] = '';
        }
        return $this->json(['data' => $data]);
    }
}
