<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\OssUpload;

class OssuploadController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		OssUpload::datainitial();
		$query = OssUpload::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id asc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');

		if ($data['status'] == 1) {
			OssUpload::where('id', '>', 0)->update(['status' => 0]);
		}
		OssUpload::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		$data['settings'] = serialize($data['settings']);

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = OssUpload::create($data);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				OssUpload::update($data);
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
		$data = OssUpload::field('*')->find($id)->toArray();
		$data['settings'] = iunserializer($data['settings']);

		if (empty($data['settings'])) {
			$data['settings'] = ['mchid' => '', 'signkey' => ''];
		}

		return $this->json(['data' => $data]);
	}
}
