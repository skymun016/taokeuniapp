<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\RegisterField;

class RegisterfieldController extends Base
{
	function getPtype()
	{
		$path = input('post.path', '', 'serach_in');
		if(!empty($path)){
			$tmppath = explode('/', $path);
			return $tmppath[2];
		}
	}

	function index()
	{
		$path = input('post.path', '', 'serach_in');
		$keyword = input('post.keyword', '', 'serach_in');
		$query = RegisterField::where(['weid' => weid()]);
		$ptype = $this->getPtype();

		if ($ptype == 'users') {
			$ptype = 'member';
		}

		RegisterField::datainitial($ptype);
		$query->where('ptype', $ptype);
		if (!empty($keyword)) {
			$query->where('viewmingcheng', 'like', '%' . $keyword . '%');
		}

		$Fielddata = $query->order('sort asc,id asc')->select()->toArray();

		$data['data'] = $Fielddata;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,is_listView,is_input,is_front,is_frontinput,is_import,is_search,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		RegisterField::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$data = input('post.');
		
		if(empty($data['inputtype'])){
			throw new ValidateException('请选择输入方式');
		}

		if(!empty($data['selectvalue'])){
			$data['selectvalue'] = implode(',', $data['selectvalue']);
		}else{
			$data['selectvalue'] = '';
		}


		if(!empty($data['valuerules'])){
			$data['valuerules'] = implode('|', $data['valuerules']);
		}else{
			$data['valuerules'] = '';
		}

		unset($data['create_time']);

		$id = $this->request->post('id');
		if (empty($id)) {

			$data['weid'] = weid();
			$path = input('post.path', '', 'serach_in');

			$data['ptype'] = $this->getPtype();

			if ($data['ptype'] == 'users') {
				$data['ptype'] = 'member';
			}

			try {
				$res = RegisterField::create($data);
				if ($res->id && empty($data['sort'])) {
					RegisterField::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			try {
				RegisterField::update($data);
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
		$data = RegisterField::field('*')->find($id)->toArray();
		if(!empty($data['selectvalue'])) {
			$data['selectvalue'] = explode(',', $data['selectvalue']);
		}
		if(!empty($data['valuerules'])) {
			$data['valuerules'] = explode('|', $data['valuerules']);
		}else{
			$data['valuerules'] = [];
		}

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new RegisterField());
	}
}
