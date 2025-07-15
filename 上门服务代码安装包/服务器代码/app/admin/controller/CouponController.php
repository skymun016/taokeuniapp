<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Coupon;
use app\model\Category;
use app\model\Goods;

class CouponController extends Base
{

	function index()
	{
		$weid = weid();
		$page = input('post.page', 1, 'intval');
		$keyword = input('post.keyword', '', 'serach_in');
		$status = input('post.status', '', 'serach_in');
		$query = Coupon::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		if (!empty($status) || $status === "0") {
			$query->where(['status' => $status]);
		}
		if (!empty($this->sid)) {
			$query->where('sid', $this->sid);
		}

		$res = $query->order('sort asc,id desc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			if ($vo['coupon_type'] == 10) {
				$vo['couponway'] = '满：' . $vo['min_price'] . '元,减：' . $vo['reduce_price'] . '元';
			} elseif ($vo['coupon_type'] == 20) {
				$vo['couponway'] = '满：' . $vo['min_price'] . '元,打：' . $vo['discount'] . '折';
			}

			if ($vo['expire_type'] == 10) {

				if ($vo['expire_day'] > 0) {
					$vo['expire_day'] = $vo['expire_day'] . "天";
				} else {
					$vo['expire_day'] = "长期";
				}

				$vo['expire_type'] = '领取后：' . $vo['expire_day'] . '有效';;
			} elseif ($vo['expire_type'] == 20) {

				$vo['expire_type'] = '有效期：' . time_format($vo['start_time']) . '到' . time_format($vo['end_time']);
			}

			$vo['ptype'] = getCouponPtype($vo['ptype']);
			$vo['coupon_type'] = getCouponType($vo['coupon_type']);
			$vo['color'] = getColor($vo['color']);
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Coupon::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		$data['start_time'] =  xm_strtotime($data['start_time']);
		
		$data['end_time'] =  xm_strtotime($data['end_time']);

		if (empty($id)) {
			$data['weid'] = weid();
			if (!empty($this->sid)) {
				$data['sid'] = $this->sid;
			}
			try {
				$res = Coupon::create($data);
				if ($res->id && empty($data['sort'])) {
					Coupon::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				Coupon::update($data);
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
		$data = Coupon::field('*')->find($id)->toArray();

		if ($data['use_goods']==2 && !empty($data['goods_ids'])) {
			$goods = Goods::find($data['goods_ids']);
			if (!empty($goods)) {
				$data['goods'] = $goods->toArray();
			}
		}else{
			$data['goods'] = [];
		}

		if (empty($data['start_time'])) {
			$data['start_time'] = "";
		} else {
			$data['start_time'] = time_ymd($data['start_time']);
		}
		if (empty($data['end_time'])) {
			$data['end_time'] = "";
		} else {
			$data['end_time'] = time_ymd($data['end_time']);
		}


		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new Coupon());
	}
	function getField()
	{
		
		$data['colorarray'] = getColor();
		$data['ptypearray'] = getCouponPtype();
		$data['coupon_typearray'] = getCouponType();
		$data['expire_typearray'] = getExpireType();
		$data['cat_idsarray'] = _generateSelectTree(Category::getpcarray());
		
		return $this->json(['data' => $data]);
	}
}
