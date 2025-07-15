<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\TransportExtend;

class TransportextendController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = trim(input('post.keyword', '', 'serach_in'));
		$status = trim(input('post.status', '', 'serach_in'));

		$where = [];
		$where['weid'] = $weid;
		if ($status !== '') {
			$where['status'] = $status;
		}

		$field = '*';
		$query = TransportExtend::where($where);

		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$query->field($field)->order('id asc');

		$datalist = $query->paginate(getpage())->toArray();

		foreach ($datalist['data'] as &$vo) {
			if ($vo['is_default'] == 1) {
				$vo['area_name'] = '[默认]' . $vo['area_name'];
			}
		}

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,snum,sprice,xnum,xprice,sort,is_default,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		TransportExtend::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{

		$id = $this->request->post('id');
		$data = input('post.');
		if(!empty($data['area_id'])){
			$data['area_id'] = implode(',', $data['area_id']);
		}

		if (empty($id)) {
			$data['weid'] = weid();
			$res = TransportExtend::create($data);
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			TransportExtend::update($data);
			return $this->json(['msg' => '修改成功']);
		}
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) $this->error('参数错误');
		$res = TransportExtend::find($id);
		if (!empty($res)) {
			$res = $res->toArray();
		}
		$res['area_id'] = explode(',',$res['area_id']);
		return $this->json(['data' => $res]);
	}

	function delete()
	{
		return $this->del(new TransportExtend());
	}
}
