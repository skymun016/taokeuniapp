<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\DiyPage;
use app\model\DomainReplace;
use think\db\Where;

class DiypageController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = DiyPage::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('id asc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}
	public function add()
	{
		$weid = weid();
		$data['weid'] = $weid;
		$data['title'] = '自定义页面';
		$data['version'] = 2;
		$data['pagebase'] = serialize(input('post.pagebase'));
		$res = DiyPage::create($data);
		return $this->json(['msg' => '添加成功', 'data' => $res->id]);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$weid = weid();
		$postdata = input('post.');
		unset($data['create_time']);

		if (!empty($postdata['pageList'])) {
			foreach ($postdata['pageList'] as $vo) {

				$dp	= DiyPage::where(['weid' => $weid, 'id' => $vo['id'], 'version' => 2])->find();
				$data['weid'] = $weid;
				$data['title'] = $vo['pagebase'][0]['params']['title'];
				$data['pagebase'] = serialize($vo['pagebase']);
				$data['modulelist'] = serialize($vo['modulelist']);
				$data['version'] = 2;
				$data['status'] = 1;
				if (empty($dp)) {
					try {
						$res = DiyPage::create($data);
					} catch (\Exception $e) {
						throw new ValidateException($e->getMessage());
					}
				} else {
					$data['id'] = $vo['id'];
					try {
						DiyPage::update($data);
					} catch (\Exception $e) {
						throw new ValidateException($e->getMessage());
					}
				}
			}
			return $this->json(['msg' => '保存成功']);
		} else {
			if (empty($id)) {
				$data['weid'] = $weid;
				$data['title'] = $postdata['title'];
				$data['is_index'] = $postdata['is_index'];
				$data['status'] = $postdata['status'];
				try {
					$res = DiyPage::create($data);
				} catch (\Exception $e) {
					throw new ValidateException($e->getMessage());
				}

				return $this->json(['msg' => '添加成功', 'data' => $res->id]);
			} else {

				try {
					DiyPage::update($data);
				} catch (\Exception $e) {
					throw new ValidateException($e->getMessage());
				}
				return $this->json(['msg' => '修改成功']);
			}
		}
	}

	public function listUpdate()
	{
		$data = only('id,status,is_submitaudit,is_index');
		if (!$data['id']) throw new ValidateException('参数错误');
		DiyPage::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function setaudit()
	{

		$id = input('post.id');
		DiyPage::where('id', '>', 0)->where(['weid' => weid()])->where(['version' => 2])->where(['version' => 2])->update(['is_submitaudit' => 0]);
		DiyPage::where('id', '=', $id)->update(['is_submitaudit' => 1]);

		return $this->json(['msg' => '设为审核成功']);
	}

	public function setindex()
	{

		$id = input('post.id');
		DiyPage::where('id', '>', 0)->where(['weid' => weid()])->update(['is_index' => 0]);
		DiyPage::where('id', '=', $id)->update(['is_index' => 1]);

		return $this->json(['msg' => '设置首页成功']);
	}

	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$data = DiyPage::field('*')->find($id)->toArray();

		return $this->json(['data' => $data]);
	}
	function getpageInfo()
	{
		$text = [
			'subtitle' => '',
			'miaoshu' => '',
			'show' => false,
			'defaultstyle' => false,
			'fontsize' => 14,
			'color' => '#333333',
			'bold' => false,
			'italics' => false,
			'underline' => false
		];
		$link = [
			'ptype' => '',
			'miaoshu' => '',
			'id' => '',
			'path' => ''
		];
		$base = [
			'style' => 0,
			'margin' => true,
			'bg' => 1,
			'bt' => 1,
			'bc' => '#ffffff',
			'bi' => '',
			'fc' => ''
		];

		$data = DiyPage::where(['weid' => weid(), 'version' => 2])->select()->toArray();
		if (!empty($data)) {
			foreach ($data as &$vo) {
				$vo['title'] = $vo['title'];
				$vo['pagebase'] = iunserializer($vo['pagebase']);
				if (empty($vo['pagebase'][0]['base']['titleBackground'])) {
					$vo['pagebase'][0]['base']['titleBackground'] = $base;
				}
				$vo['modulelist'] = iunserializer($vo['modulelist']);

				foreach ($vo['modulelist'] as &$movo) {
					if ($movo['type'] == 'goods') {
						if (empty($movo['title']['title'])) {
							$movo['title']['title'] = $text;
							$movo['title']['title']['prompt'] = '模块标题';
							$movo['title']['title']['txt'] = '商品模块标题';

							$movo['title']['more'] = $text;
							$movo['title']['more']['prompt'] = '更多文字';
							$movo['title']['more']['txt'] = '更多';
							$movo['title']['link'] = $link;
						}
						if(empty($movo['base']['auto']['ptype'])){
							$movo['base']['auto']['ptype'] = 0;
						}
					}
					if ($movo['type'] == 'tuan') {
						if (empty($movo['title']['title'])) {
							$movo['title']['title'] = $text;
							$movo['title']['title']['prompt'] = '模块标题';
							$movo['title']['title']['txt'] = '超值拼团';
							$movo['title']['title']['show'] = true;

							$movo['title']['more'] = $text;
							$movo['title']['more']['prompt'] = '更多文字';
							$movo['title']['more']['txt'] = '更多';
							$movo['title']['more']['show'] = true;
						}
					}
					if ($movo['type'] == 'miaosha') {
						if (empty($movo['title']['title'])) {
							$movo['title']['title'] = $text;
							$movo['title']['title']['prompt'] = '模块标题';
							$movo['title']['title']['txt'] = '限时秒杀';
							$movo['title']['title']['show'] = true;

							$movo['title']['more'] = $text;
							$movo['title']['more']['prompt'] = '更多文字';
							$movo['title']['more']['txt'] = '更多';
							$movo['title']['more']['show'] = true;
						}
					}
					if ($movo['type'] == 'window') {
						if (empty($movo['base']['style'])) {
							$movo['base']['style'] = 'style1';
						}
						foreach ($movo['list'] as &$wlistvo) {
							if (empty($wlistvo['type'])) {
								$wlistvo['type'] = "";
							}
							if (empty($wlistvo['windowbody'])) {
								$wlistvo['windowbody'] = [];
							}
						}
					}
				}
			}
		} else {
			$data = '';
		}

		$data = DomainReplace::setreplace($data);

		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new DiyPage());
	}
}
