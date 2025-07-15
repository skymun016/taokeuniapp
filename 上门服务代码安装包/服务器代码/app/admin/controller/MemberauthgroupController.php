<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\MemberAuthGroup;
use app\model\MemberCommission;
use app\model\Coupon;
use app\model\GoodsGiftcardType;
use app\model\Goods;

class MemberauthgroupController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = MemberAuthGroup::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$datalist = $query->order('sort asc,id asc')->select()->toArray();

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,is_lookprice,is_buyright,is_default,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		MemberAuthGroup::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = MemberAuthGroup::create($data);
				if ($res->id && empty($data['sort'])) {
					MemberAuthGroup::update(['sort' => $res->id, 'id' => $res->id]);
				}
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				MemberAuthGroup::update($data);
				$this->_synupdata($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}
	function _synupdata($data)
	{
		MemberCommission::where('mgid', $data['id'])->delete();

		if (!empty($data['membercommission'])) {
			foreach ($data['membercommission'] as $mcvo) {
				if ($mcvo['return_percent'] > 0) {
					MemberCommission::create([
						'mgid' => (int) $data['id'],
						'commission_method' => $data['commission_method'],
						'roletype' => $mcvo['roletype'],
						'return_percent' => $mcvo['return_percent']
					]);
				}
			}
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$data = MemberAuthGroup::field('*')->find($id)->toArray();

		if (!empty($data['upgrade_goods_id'])) {
			$upgrade_goods = Goods::find($data['upgrade_goods_id']);
			if (!empty($upgrade_goods)) {
				$data['upgrade_goods'] = $upgrade_goods->toArray();
			}
		}

		$member_commission = MemberCommission::where('mgid', $id)->select()->toArray();
		foreach ($member_commission as $key => $vo) {
			$mc[$vo['roletype']] = $vo['return_percent'];
		}

		$data['membercommission'] = getCommissionType();

		foreach ($data['membercommission'] as &$vo) {
			$vo['return_percent'] = $mc[$vo['roletype']];
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new MemberAuthGroup());
	}
	function getField()
	{
		$data['couponarray'] = Coupon::getpcarray(2);
		$data['giftcardarray'] = GoodsGiftcardType::getpcarray();
		return $this->json(['data' => $data]);
	}
	function getCommissionType()
	{
		$data['membercommission'] = getCommissionType();
		return $this->json(['data' => $data]);
	}
}
