<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Printer;

class PrinterController extends Base
{
	public function update()
	{

		$data = input('post.');
		$data['settings'] = serialize($data);
		unset($data['create_time']);
		$data['weid'] = weid();
		if (!empty($this->sid)) {
			$data['sid'] = (int) $this->sid;
		}
		if (empty($data['id'])) {
			try {
				$res = Printer::create($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '操作成功', 'data' => $res->id]);
		} else {

			try {
				Printer::update($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}
			return $this->json(['msg' => '操作成功']);
		}
	}

	function getInfo()
	{
		$data = Printer::where(['weid' => weid(), 'sid' => (int) $this->sid])->order('id desc')->find();
		$res = iunserializer($data['settings']);
		$res['id'] = $data['id'];
		$res['pinpai'] = $data['pinpai'];

		return $this->json(['data' => $res]);
	}
	function getField()
	{
		$data['PinpaiType'] = Printer::getpcarray();
		return $this->json(['data' => $data]);
	}
}
