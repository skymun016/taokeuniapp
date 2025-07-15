<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Goods;
use app\model\Category;
use app\model\GoodsDescription;
use app\model\GoodsImage;
use app\model\GoodsSku;
use app\model\GoodsSkuValue;
use app\model\Store;
use app\model\MemberAuthGroup;
use app\model\GoodsMemberDiscount;
use app\model\GoodsTimeDiscount;
use app\model\Operatingcity;
use app\model\Tuanzhang;
use app\model\GoodsGiftcardType;

class GoodsController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$path = input('post.path', '', 'serach_in');
		$ptypeistimes = input('post.ptypeistimes', '', 'serach_in');
		$ptype = input('post.ptype', '', 'serach_in');
		$is_times = input('post.is_times', '', 'serach_in');
		$is_all = input('post.is_all', '', 'serach_in');
		$status = input('post.status', '', 'serach_in');

		$keyword = input('post.keyword', '', 'serach_in');
		$cat_id = input('post.cat_id', '', 'serach_in');
		$is_recommended = input('post.is_recommended', '', 'serach_in');

		if ($ptypeistimes == 1) {
			$ptype = 1;
		} elseif ($ptypeistimes == 2) {
			$ptype = 2;
		} elseif ($ptypeistimes == 3) {
			$is_times = 1;
		}

		if ($path == '/order/service' || $path == '/order/storeservice') {
			$ptype = 2;
		}

		$query = Goods::where(['weid' => $weid]);

		if (!empty($ptype)) {
			$query->where('ptype', $ptype);
		} else {
			$query->where('ptype', '>', 0);
		}

		if (empty($is_all)) {
			if (!empty($is_times)) {
				$query->where('is_times', $is_times);
			} else {
				$query->where('is_times', 0);
			}
		}

		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		if (!empty($this->tzid)) {
			$query->where('sid', Store::getidbytzid($this->tzid));
		}

		if (!empty($this->ocid)) {
			$Operatingcitydata = Operatingcity::find($this->ocid);
			if ($Operatingcitydata) {
				$Operatingcitydata = $Operatingcitydata->toArray();
				if (empty($Operatingcitydata['areatype'])) {
					$Operatingcitydata['areatype'] = 3;
				}

				if ($Operatingcitydata['areatype'] == 3) {
					$query->where('district_name', $Operatingcitydata['district_name']);
				} elseif ($Operatingcitydata['areatype'] == 2) {
					$query->where('city_name', $Operatingcitydata['city_name']);
				} elseif ($Operatingcitydata['areatype'] == 1) {
					$query->where('province_name', 'like', '%' . $Operatingcitydata['province_name'] . '%');
				}
			}
		}

		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		if (!empty($cat_id)) {
			$query->where('cat_id',  $cat_id);
		}

		if (!empty($is_recommended) || $is_recommended === "0") {
			$query->where('is_recommended', $is_recommended);
		}

		if (!empty($status) || $status === "0") {
			$query->where(['status' => $status]);
		}

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		$sql = $query->getLastsql();

		foreach ($res['data'] as &$vo) {
			$vo['image'] = toimg($vo['image']);

			if ($vo['time_amount'] == 0) {
				$vo['time_amount'] = '无';
			}

			if ($vo['sid'] == 0) {
				$vo['name'] = '[自营]' . $vo['name'];
			} else {
				$StoreTitle = Store::getTitle($vo['sid']);
				if (empty($StoreTitle)) {
					$vo['name'] = '[商户已被删除]' . $vo['name'];
				} else {
					$vo['name'] = '[' . $StoreTitle . ']' . $vo['name'];
				}
			}
			if ($vo['ptype'] == 2) {
				$vo['quantity'] = $vo['timesmum'];
			}
			if (empty($vo['quantity'])) {
				$vo['quantity'] = 1;
			}

			$vo['cat_id'] = Category::getTitle($vo['cat_id']);
		}
		$data['data'] = $res;
		if ($page == 1) {
			$data['field_data']['cidarray'] = _generateSelectTree(Category::getpcarray());
		}
		$data['sql'] = $sql;
		return $this->json($data);
	}

	function cashregister()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$path = input('post.path', '', 'serach_in');
		$ptypeistimes = input('post.ptypeistimes', '', 'serach_in');
		$ptype = input('post.ptype', '', 'serach_in');
		$status = input('post.status', '', 'serach_in');

		$keyword = input('post.keyword', '', 'serach_in');
		$cat_id = input('post.cat_id', '', 'serach_in');
		$is_recommended = input('post.is_recommended', '', 'serach_in');

		if ($ptypeistimes == 1) {
			$ptype = 1;
		} elseif ($ptypeistimes == 2) {
			$ptype = 2;
		}

		$ptype = 2;

		$query = Goods::where(['weid' => $weid]);

		if (!empty($ptype)) {
			$query->where('ptype', $ptype);
		} else {
			$query->where('ptype', '>', 0);
		}

		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		if (!empty($this->tzid)) {
			$query->where('sid', Store::getidbytzid($this->tzid));
		}

		if (!empty($this->ocid)) {
			$Operatingcitydata = Operatingcity::find($this->ocid);
			if ($Operatingcitydata) {
				$Operatingcitydata = $Operatingcitydata->toArray();
				if (empty($Operatingcitydata['areatype'])) {
					$Operatingcitydata['areatype'] = 3;
				}

				if ($Operatingcitydata['areatype'] == 3) {
					$query->where('district_name', $Operatingcitydata['district_name']);
				} elseif ($Operatingcitydata['areatype'] == 2) {
					$query->where('city_name', $Operatingcitydata['city_name']);
				} elseif ($Operatingcitydata['areatype'] == 1) {
					$query->where('province_name', 'like', '%' . $Operatingcitydata['province_name'] . '%');
				}
			}
		}

		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		if (!empty($cat_id)) {
			$cat_id = Category::getsonid($cat_id);
			$query->where(['cat_id' => $cat_id]);
		}

		if (!empty($is_recommended) || $is_recommended === "0") {
			$query->where('is_recommended', $is_recommended);
		}

		if (!empty($status) || $status === "0") {
			$query->where(['status' => $status]);
		}

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		$sql = $query->getLastsql();

		foreach ($res['data'] as &$vo) {
			$vo['image'] = toimg($vo['image']);

			if ($vo['time_amount'] == 0) {
				$vo['time_amount'] = '无';
			}
			if ($vo['ptype'] == 2) {
				$vo['quantity'] = $vo['timesmum'];
			}
			if (empty($vo['quantity'])) {
				$vo['quantity'] = 1;
			}

			if ($vo['is_times'] == 3 && !empty($vo['card_tid'])) {

				$GoodsGiftcardType = GoodsGiftcardType::find((int) $vo['card_tid']);
				if (!empty($GoodsGiftcardType)) {
					$GoodsGiftcardType = $GoodsGiftcardType->toArray();
				}
				$vo['price'] = $GoodsGiftcardType['buy_price'];
			}

			$vo['cat_id'] = Category::getTitle($vo['cat_id']);
		}
		$data['data'] = $res;
		if ($page == 1) {
			$data['field_data']['cidarray'] = _generateSelectTree(Category::getpcarray());
		}
		$data['sql'] = $sql;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,is_recommended,is_additional,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Goods::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = request()->post('id');
		$data = input('post.');
		unset($data['create_time']);
		$data['image'] = $data['images'][0]['url'];
		if (!empty($data['keyword'])) {
			$data['keyword'] = implode(',', $data['keyword']);
		}
		$data['time_amount'] = intval($data['time_amount']);
		//判断is_timer是否为1
		if ($data['is_timer'] == 1) {
			if (empty($data['time_amount'])) {
				throw new ValidateException('服务时长不能为空');
			}
			$data['quantity_unit'] = '分钟';
		}

		if ($data['cat_id']) {
			$cat = Category::find($data['cat_id']);
			if (!empty($cat)) {
				$cat = $cat->toArray();
			}
			$data['ptype'] = $cat['ptype'];
		}
		if (!empty($data['videourl'])) {
			$data['videotype'] = 1;
		} elseif (!empty($data['videoid'])) {
			$data['videotype'] = 2;
		} else {
			$data['videotype'] = 0;
		}

		if (empty($id)) {
			if (empty($data['cat_id'])) {
				throw new ValidateException('请选择分类');
			}
			if ($data['is_times'] == 3) {
				if (empty($data['card_tid'])) {
					throw new ValidateException('请选择卡类型');
				}
			}
			$data['weid'] = weid();
			if (!empty($this->sid)) {
				$data['sid'] = $this->sid;

				$storemod = Store::find($this->sid);
				if ($storemod) {
					$data['province_name'] = $storemod->province_name;
					$data['city_name'] = $storemod->city_name;
					$data['district_name'] = $storemod->district_name;
				}
			}

			if (!empty($this->tzid)) {
				$data['sid'] = Store::getidbytzid($this->tzid);
				$Tuanzhangmod = Tuanzhang::find($this->tzid);
				if ($Tuanzhangmod) {
					$data['province_name'] = $Tuanzhangmod->province_name;
					$data['city_name'] = $Tuanzhangmod->city_name;
					$data['district_name'] = $Tuanzhangmod->district_name;
				}
			}

			if (!empty($this->ocid)) {
				$data['ocid'] = $this->ocid;
				if (!empty($this->ocid)) {
					$ocmod = Operatingcity::find($this->ocid);
					if ($ocmod) {
						$data['province_name'] = $ocmod->province_name;
						$data['city_name'] = $ocmod->city_name;
						$data['district_name'] = $ocmod->district_name;
					}
				}
			}

			if (empty($data['tel'])) {
				$data['tel'] = '';
			}
			try {
				$res = Goods::create($data);
				if ($res->id && empty($data['sort'])) {
					Goods::update(['sort' => $res->id, 'id' => $res->id]);
				}
				$data['id'] = $res->id;
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				Goods::update($data);
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}

	function _synupdata($data)
	{
		//详情
		if (empty(GoodsDescription::where('goods_id', $data['id'])->find())) {
			GoodsDescription::create([
				'goods_id' => (int) $data['id'],
				'description' => $data['description']
			]);
		} else {
			GoodsDescription::where('goods_id', $data['id'])->update(['description' => $data['description']]);
		}

		//sku
		GoodsSku::where('goods_id', $data['id'])->delete();
		if (isset($data['attribute'])) {
			foreach ($data['attribute'] as $attr) {
				GoodsSku::create([
					'goods_id' => (int) $data['id'],
					'name' => $attr['name'],
					'ptype' => 'radio',
					'item' => $attr['item'] ? implode(',', $attr['item']) : ''
				]);
			}
		}

		GoodsSkuValue::where('goods_id', $data['id'])->delete();
		if (isset($data['sku'])) {
			foreach ($data['sku'] as $skuarr) {
				GoodsSkuValue::create([
					'goods_id' => (int) $data['id'],
					'sku' => $skuarr['sku'],
					'image' => $skuarr['image'],
					'quantity' => $skuarr['quantity'],
					'price' => $skuarr['price']
				]);
			}
		}

		GoodsMemberDiscount::where('goods_id', $data['id'])->delete();
		if (!empty($data['is_member_discount']) && !empty($data['MemberGroup'])) {
			foreach ($data['MemberGroup'] as $mgvo) {
				if ($mgvo['price'] > 0) {
					GoodsMemberDiscount::create([
						'goods_id' => (int) $data['id'],
						'mgid' => (int) $mgvo['id'],
						'price' => (float) $mgvo['price'],
						'is_free' => (int) $mgvo['is_free']
					]);
				}
			}
		}
		GoodsTimeDiscount::where('goods_id', $data['id'])->delete();
		if (!empty($data['timediscount'])) {
			foreach ($data['timediscount'] as $tdvo) {
				if ($tdvo['price'] > 0) {
					GoodsTimeDiscount::create([
						'discount_method' => 1,
						'goods_id' => (int) $data['id'],
						'begin_time' => $tdvo['begin_time'],
						'end_time' =>  $tdvo['end_time'],
						'addsubtract' => (int) $tdvo['addsubtract'],
						'price' => (float) $tdvo['price']
					]);
				}
			}
		}
		//图片
		GoodsImage::where('goods_id', $data['id'])->delete();
		if (isset($data['images'])) {
			foreach ($data['images'] as $image) {
				GoodsImage::create([
					'goods_id' => (int) $data['id'],
					'weid' => weid(),
					'image' => $image['url']
				]);
			}
		}
	}

	function getgoodssku()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		$data['attribute'] = GoodsSku::get_goods_sku($id);
		$data['sourceAttribute'] = $data['attribute'];
		return $this->json(['data' => $data]);
	}

	public function price()
	{

		$id = input('post.goodsId', '', 'serach_in');
		$msid = input('post.msid', '', 'serach_in');
		$tuanid = input('post.tuanid', '', 'serach_in');
		$sku = input('post.sku');
		$goodsmob = new Goods;

		$goodsPrice = $goodsmob->cartGoods([
			'id' => $id,
			'sku' => $sku,
			'msid' => $msid,
			'tuanid' => $tuanid
		]);

		$data['price'] = $goodsPrice['total'];
		$data['points'] = $goodsPrice['total_return_points'];

		if ($goodsPrice['ptype'] == 1) {
			$data['stores'] = $goodsPrice['stores'];
		} elseif ($goodsPrice['ptype'] == 2) {
			$data['stores'] = 999999;
		}

		if ($goodsPrice['tuan']['buy_max']) {
			$data['stores'] = $goodsPrice['tuan']['buy_max'];
		}
		if ($goodsPrice['miaosha']['buy_max']) {
			$data['stores'] = $goodsPrice['miaosha']['buy_max'];
		}

		$data['image'] = $goodsPrice['image'];
		if ($goodsPrice['skuimage']) {
			$data['image'] = $goodsPrice['skuimage'];
		}
		return $this->json(['data' => $data]);
	}

	function getCashregisterInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		//if (!$id) throw new ValidateException('参数错误');
		if ($id) {
			$data = Goods::field('*')->find($id)->toArray();
		} else {
			$data = [
				'is_times' => 0,
				'sort' => 100,
				'status' => 1,
			];
		}
		if (!empty($data['keyword'])) {
			$data['keyword'] = explode(',', $data['keyword']);
		} else {
			$data['keyword'] = [];
		}

		$GD = GoodsDescription::where(['goods_id' => $id])->find();

		if (!empty($GD)) {
			$data['description'] = \app\model\DomainReplace::setreplace($GD->description);
		}

		$goods_image = GoodsImage::where(['goods_id' => $id])
			->field('image')
			->order('id asc')
			->select()->toArray();

		if (!empty($goods_image)) {
			foreach ($goods_image as $key => $vo) {
				$data['images'][$key]['url'] = toimg($vo['image']);
			}
		}

		$data['attribute'] = GoodsSku::get_goods_sku($id);
		$data['sourceAttribute'] = $data['attribute'];

		$MGDiscountarray = GoodsMemberDiscount::where(['goods_id' => $id])->select()->toArray();

		if (!empty($MGDiscountarray)) {
			$MGDiscount = [];
			foreach ($MGDiscountarray as $vo) {
				$MGDiscount[$vo['mgid']] = $vo;
			}
		}

		$data['MemberGroup'] = MemberAuthGroup::getGroup();
		foreach ($data['MemberGroup'] as &$mvo) {
			if (!empty($MGDiscount[$mvo['id']])) {
				$mvo['price'] = $MGDiscount[$mvo['id']]['price'];
				$mvo['addsubtract'] = $MGDiscount[$mvo['id']]['addsubtract'];
				$mvo['is_free'] = $MGDiscount[$mvo['id']]['is_free'];
			}
		}

		$timediscount = GoodsTimeDiscount::where(['goods_id' => $id])->select()->toArray();

		if (!empty($timediscount)) {
			$data['timediscount'] = $timediscount;
		} else {
			$data['timediscount'] = [['begin_time' => '', 'end_time' => '', 'addsubtract' => '', 'price' => '']];
		}

		$data['sku'] = GoodsSkuValue::field('sku,image,quantity,price')->where(['goods_id' => $id])
			->order('id asc')
			->select()->toArray();

		if ($data['is_times'] == 3 && !empty($data['card_tid'])) {

			$GoodsGiftcardType = GoodsGiftcardType::find((int) $data['card_tid']);
			if (!empty($GoodsGiftcardType)) {
				$GoodsGiftcardType = $GoodsGiftcardType->toArray();
			}
			$data['price'] = $GoodsGiftcardType['buy_price'];
		}

		return $this->json(['data' => $data]);
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		//if (!$id) throw new ValidateException('参数错误');
		if ($id) {
			$data = Goods::field('*')->find($id)->toArray();
		} else {
			$data = [
				'is_times' => 0,
				'sort' => 100,
				'status' => 1,
			];
		}
		if (!empty($data['keyword'])) {
			$data['keyword'] = explode(',', $data['keyword']);
		} else {
			$data['keyword'] = [];
		}

		$GD = GoodsDescription::where(['goods_id' => $id])->find();

		if (!empty($GD)) {
			$data['description'] = \app\model\DomainReplace::setreplace($GD->description);
		}

		$goods_image = GoodsImage::where(['goods_id' => $id])
			->field('image')
			->order('id asc')
			->select()->toArray();

		if (!empty($goods_image)) {
			foreach ($goods_image as $key => $vo) {
				$data['images'][$key]['url'] = toimg($vo['image']);
			}
		}

		$data['attribute'] = GoodsSku::get_goods_sku($id);
		$data['sourceAttribute'] = $data['attribute'];

		$MGDiscountarray = GoodsMemberDiscount::where(['goods_id' => $id])->select()->toArray();

		if (!empty($MGDiscountarray)) {
			$MGDiscount = [];
			foreach ($MGDiscountarray as $vo) {
				$MGDiscount[$vo['mgid']] = $vo;
			}
		}

		$data['MemberGroup'] = MemberAuthGroup::getGroup();
		foreach ($data['MemberGroup'] as &$mvo) {
			if (!empty($MGDiscount[$mvo['id']])) {
				$mvo['price'] = $MGDiscount[$mvo['id']]['price'];
				$mvo['addsubtract'] = $MGDiscount[$mvo['id']]['addsubtract'];
				$mvo['is_free'] = $MGDiscount[$mvo['id']]['is_free'];
			}
		}

		$timediscount = GoodsTimeDiscount::where(['goods_id' => $id])->select()->toArray();

		if (!empty($timediscount)) {
			$data['timediscount'] = $timediscount;
		} else {
			$data['timediscount'] = [['begin_time' => '', 'end_time' => '', 'addsubtract' => '', 'price' => '']];
		}

		$data['sku'] = GoodsSkuValue::field('sku,image,quantity,price')->where(['goods_id' => $id])
			->order('id asc')
			->select()->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new Goods());
	}
	function getField()
	{
		$ptype = input('post.ptype', '', 'serach_in');
		$data['cidarray'] = _generateSelectTree(Category::getpcarray($ptype));
		$data['card_tidarray'] = \app\model\GoodsGiftcardType::getpcarray();
		$data['unitarray'] = \app\model\GoodsQuantityUnit::getpcarray($ptype);

		return $this->json(['data' => $data]);
	}
}
