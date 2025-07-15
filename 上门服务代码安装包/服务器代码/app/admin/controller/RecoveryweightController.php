<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\RecoveryWeight;

class RecoveryweightController extends Base
{

	function index()
	{
		RecoveryWeight::datainitial();
		$weid = weid();
		$query = RecoveryWeight::where(['weid' => $weid]);

		$datalist = $query->order('sort asc,id asc')->select()->toArray();

		$data['data'] = $datalist;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,quantity,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		RecoveryWeight::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		$data['ptype'] = (int) str_ireplace('aaa', '', $data['ptype']);
		unset($data['create_time']);

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = RecoveryWeight::create($data);
				if ($res->id && empty($data['sort'])) {
					RecoveryWeight::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				RecoveryWeight::update($data);
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
		$data = RecoveryWeight::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new RecoveryWeight());
	}
}
