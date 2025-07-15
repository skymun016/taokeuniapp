<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\BargainGoods;
use app\model\BargainGoodsSkuValue;
use app\model\GoodsSku;
use app\model\Goods;

class BargaingoodsController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = BargainGoods::where(['weid' => $weid]);
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
		BargainGoods::update($data);

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
				$res = BargainGoods::create($data);
				if ($res->id && empty($data['sort'])) {
					BargainGoods::update(['sort' => $res->id, 'id' => $res->id]);
				}
				$data['id'] = $res->id;
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				BargainGoods::update($data);
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}
	function _synupdata($data)
	{
		BargainGoodsSkuValue::where(['bargain_id' => $data['id'], 'goods_id' => $data['goods_id']])->delete();
		if (isset($data['sku'])) {
			foreach ($data['sku'] as $skuarr) {
				BargainGoodsSkuValue::create([
					'bargain_id' => (int) $data['id'],
					'goods_id' => (int) $data['goods_id'],
					'sku' => $skuarr['sku'],
					'image' => $skuarr['image'],
					'quantity' => $skuarr['quantity'],
					'price' => $skuarr['price']
				]);
			}
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$data = BargainGoods::field('*')->find($id)->toArray();
		if(!empty($data['goods_id'])){
			$goods = Goods::find($data['goods_id']);
			if(!empty($goods)){
				$data['goods'] = $goods->toArray();
			}
		}
		$data['attribute'] = GoodsSku::get_goods_sku($data['goods_id']);
		$data['sourceAttribute'] = $data['attribute'];

		$data['sku'] = BargainGoodsSkuValue::field('sku,image,quantity,price')->where(['bargain_id' => $id, 'goods_id' => $data['goods_id']])
			->order('id asc')
			->select()->toArray();
		
		$data['begin_date'] = time_format($data['begin_date']);
		$data['end_date'] = time_format($data['end_date']);

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new BargainGoods());
	}
}
