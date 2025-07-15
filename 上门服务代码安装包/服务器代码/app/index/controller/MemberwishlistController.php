<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\MemberWishlist;

class MemberwishlistController extends Base
{
    public function index()
    {
        $ptype = input('post.ptype');
        $uid = input('post.uid');
        if (empty($uid)) {
            $uid = UID();
        }

        $where['weid'] = weid();
        $where['uid'] = $uid;
        $where['ptype'] = $ptype;

        $res = MemberWishlist::where($where)
            ->order('id desc')
            ->paginate(getpage())
            ->toArray();

        foreach ($res['data'] as &$vo) {
            $vo['create_time'] = time_format($vo['create_time']);
        }
        $data['data'] = $res;

        return $this->json($data);
    }

    public function detail()
    {
        $postdata = input('post.');

        $where['weid'] = weid();
        $where['uid'] = UID();
        $where['goods_id'] = (int) $postdata['goods_id'];
        $where['ptype'] = $postdata['ptype'];

        $data = MemberWishlist::where($where)->find();

        if (!empty($data)) {
            $data = $data->toArray();
        }

        return $this->json(['data' => $data]);
    }

    public function add()
    {
        $postdata = input('post.');

        $data['weid'] = weid();
        $data['uid'] = UID();
        $data['goods_id'] = (int) $postdata['goods_id'];
        $data['ptype'] = $postdata['ptype'];
        $data['title'] = $postdata['title'];
        $data['image'] = $postdata['image'];
        $data['url'] = $postdata['url'];
        try {
            MemberWishlist::where([
                'weid' => $data['weid'],
                'uid' => $data['uid'],
                'goods_id' => $postdata['goods_id'],
                'ptype' => $postdata['ptype']
            ])->delete();
            $res = MemberWishlist::create($data);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }
        return $this->json(['msg' => '收藏成功']);
    }
    public function del()
    {
        $id = input('post.id', '', 'intval');
        $ids = input('post.ids', '', 'serach_in');
        if (!empty($id)) {
            $result = MemberWishlist::where('id', $id)->delete();
        } elseif (!empty($ids)) {
            $inids = explode(',', $ids);
            MemberWishlist::where(['id' => $inids])->delete();
        }
        if ($result) {
            $message = '删除成功';
        } else {
            $message = '删除失败';
        }
        return $this->json(['message' => $message, 'data' => $data]);
    }
}
