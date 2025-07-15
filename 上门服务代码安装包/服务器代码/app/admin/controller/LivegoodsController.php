<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\LiveGoods;
use app\model\Goods;
use app\model\GoodsSku;

class LivegoodsController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = LiveGoods::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}
		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['begin_date'] = time_format($vo['begin_date']);
			$vo['end_date'] = time_format($vo['end_date']);

			if(!empty($vo['goods_id'])){
				$goods = Goods::find($vo['goods_id']);
				if(!empty($goods)){
					$vo['goods'] = $goods->toArray();
					$vo['image'] = toimg($vo['goods']['image']);
				}
			}
			
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		LiveGoods::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		$data['begin_date'] = strtotime($data['begin_date']);
		$data['end_date'] = strtotime($data['end_date']);

		if (empty($id)) {
			$data['weid'] = weid();
			if (!empty($this->sid)) {
				$data['sid'] = $this->sid;
			}
			try {
				$res = LiveGoods::create($data);
				if ($res->id && empty($data['sort'])) {
					LiveGoods::update(['sort' => $res->id, 'id' => $res->id]);
				}
				$data['id'] = $res->id;
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				LiveGoods::update($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$data = LiveGoods::field('*')->find($id)->toArray();
		if(!empty($data['goods_id'])){
			$goods = Goods::find($data['goods_id']);
			if(!empty($goods)){
				$data['goods'] = $goods->toArray();
			}
		}
		$data['attribute'] = GoodsSku::get_goods_sku($data['goods_id']);
		$data['sourceAttribute'] = $data['attribute'];
		
		$data['begin_date'] = time_format($data['begin_date']);
		$data['end_date'] = time_format($data['end_date']);

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new LiveGoods());
	}
}
