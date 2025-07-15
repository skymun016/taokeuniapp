<?php

namespace app\model;

use think\Model;

class GoodsGiftcardType extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'goods_giftcard_type';
	public static function getpcarray()
	{
		$where['weid'] = weid();
		$where['status'] = 1;

		$list =	self::field('id,name')->where($where)
			->order('sort asc')
			->select()
			->toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['name'];
		}
		return $array;
	}

	public static function getbuy_price($id)
	{

		$data =	self::find($id);
		if ($data) {
			return $data->buy_price;
		} else {
			return 0;
		}
	}

	public static function getidsbygoods($goods_id)
	{

		$weid = weid();

		$GiftcardTypequery = self::where('weid', $weid)->field('id');

		$GiftcardTypequery->where(function ($q) use ($goods_id) {
			$Goods = Goods::find($goods_id);
			if (!empty($Goods)) {
				$Goods = $Goods->toArray();
				$parentIds = Category::getParentIdsstr($Goods['cat_id']);

				$q->where('use_goods', 0)->whereOr('cat_ids', 'in', $parentIds)->whereOr('goods_ids', $goods_id);
			} else {
				$q->where('use_goods', 999);
			}
		});

		$dataidsarray =  $GiftcardTypequery->select()->toArray();

		$returndata = '';
		foreach ($dataidsarray as $vo) {
			if($returndata){
				$returndata = $returndata . "," . $vo['id'];
			}else{
				$returndata =  $vo['id'];
			}
			
		}
		return $returndata;
	}
}
