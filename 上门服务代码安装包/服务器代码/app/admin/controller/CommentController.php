<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Comment;
use app\model\Goods;
use app\model\Order;

class CommentController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$query = Comment::where(['weid' => weid()]);
		if (!empty($keyword)) {
			$query->where('content', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();
		if (!empty($res['data'])) {
			foreach ($res['data'] as &$vo) {
				$vo['goodsName'] = Goods::getGoodsName($vo['goods_id']);
			}
		}
		$data['data'] = $res;
		return $this->json($data);
	}
	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Comment::update($data);
		$comment = Comment::find($data['id']);

		if ($comment) {
			Order::where('uuid', $comment->technical_uuid)->update(['comment' => Comment::total_byuuid($comment->technical_uuid)]);
		}

		return $this->json(['msg' => '操作成功']);
	}
}
