<?php

namespace app\admin\controller\kefu;

use think\exception\ValidateException;
use app\model\Users;
use app\model\kefu\Seating;
use app\model\kefu\Seatinggroups;
use dh2y\qrcode\QRcode;

class SeatingController extends Base
{

	function index()
	{
		$keyword = input('post.keyword', '', 'serach_in');
		$status = input('post.status', '', 'serach_in');
		$weid = weid();
		$where = [];
		$where['weid'] = $weid;
		if ($status !== '') {
			$where['status'] = $status;
		}

		$field = 'id,title,touxiang,setopenidqrcode,status,px';

		$query = Seating::where($where);
		if (!empty($keyword)) {
			$query->where('title', 'like', '%' . $keyword . '%');
		}
		$res = $query->field($field)
			->order('id desc')
			->paginate(getpage())
			->toArray();

		$qrcod = new QRcode();

		foreach ($res['data'] as &$vo) {
			if (empty($vo['setopenidqrcode'])) {
				$qrcodeurl = gethost() . scriptPath() .   '/public/index.php?s=/index/member/setseatingopenid&xmtoken=' . $this->getToken() . '&seaid=' . $vo['id']. '&i=' . $weid;
				$qrcodres = $qrcod->png($qrcodeurl, false, 10)->getPath();
				$vo['setopenidqrcode'] = toimg('/public' . $qrcodres);
				Seating::update($vo);
			}
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status');
		if (!$data['id']) throw new ValidateException('参数错误');
		Seating::update($data);
		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{

		$id = $this->request->post('id');
		$data = $this->postdata();

		if (empty($id)) {
			$weid = weid();
			$data['uid'] = (int) $data['uid'];
			$data['weid'] = $weid;
			$res = Seating::create($data);
			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {
			Seating::update($data);
			return $this->json(['msg' => '修改成功']);
		}
	}

	function postdata()
	{
		$data = $this->request->post();
		if (!empty($data['kefutype'])) {
			if (in_array("is_mpkefu", $data['kefutype'])) {
				$data['is_mpkefu'] = "1";
			} else {
				$data['is_mpkefu'] = "0";
			}
			if (in_array("is_mobilekefu", $data['kefutype'])) {
				$data['is_mobilekefu'] = "1";
			} else {
				$data['is_mobilekefu'] = "0";
			}
			if (in_array("is_wxappkefu", $data['kefutype'])) {
				$data['is_wxappkefu'] = "1";
			} else {
				$data['is_wxappkefu'] = "0";
			}
			if (in_array("is_webkefu", $data['kefutype'])) {
				$data['is_webkefu'] = "1";
			} else {
				$data['is_webkefu'] = "0";
			}
		}

		if (!empty($data['week'])) {
			$arr = $data['week'];
			foreach ($arr as $key => $v) {
				$week .= $v . ',';
			}
			$slweek = rtrim($week, ",");
			$data['week'] = $slweek;
		}
		if (empty($data['week'])) {
			$data['week'] = '0';
		}

		return $data;
	}
	function getInfo()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) $this->error('参数错误');
		$res = Seating::find($id)->toArray();

		if ($res['is_mpkefu']) {
			$res['kefutype'][] = 'is_mpkefu';
		}
		if ($res['is_mobilekefu']) {
			$res['kefutype'][] = 'is_mobilekefu';
		}
		if ($res['is_wxappkefu']) {
			$res['kefutype'][] = 'is_wxappkefu';
		}
		if ($res['is_webkefu']) {
			$res['kefutype'][] = 'is_webkefu';
		}

		$res['week'] = explode(',', $res['week']);

		return $this->json(['data' => $res]);
	}

	function getField()
	{
		$data['userslist'] = Users::getallarray();
		$data['groups'] = Seatinggroups::getallarray();
		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new Seating());
	}
}
