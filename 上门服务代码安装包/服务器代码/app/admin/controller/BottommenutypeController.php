<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\BottomMenuType;

class BottommenutypeController extends Base
{

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = BottomMenuType::create($data);
				if ($res->id && empty($data['sort'])) {
					BottomMenuType::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				BottomMenuType::update($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '修改成功']);
		}
	}
	function getpclist()
	{

		$BottomMenu = BottomMenuType::getpagearray();
		$alldata[0]['val'] = '0';
		$alldata[0]['key'] = '默认';
		if (!empty($BottomMenu)) {
			$data = array_merge($alldata, $BottomMenu);
		} else {
			$data = $alldata;
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		$idx =  $this->request->post('id', '', 'serach_in');
		$idx = str_ireplace('aaa', '', $idx);
		if (!$idx) throw new ValidateException('参数错误');
		if (!is_array($idx)) {
			$idx = explode(',', $idx);
		}
		BottomMenuType::destroy(['id' => $idx], true);
		return $this->json(['msg' => '操作成功']);
	}
}
