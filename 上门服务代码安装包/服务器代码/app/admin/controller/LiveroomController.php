<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\LiveRoom;
use app\model\LiveAnchor;

class LiveroomController extends Base
{

	function index()
	{
		$weid = weid();
		$keyword = input('post.keyword', '', 'serach_in');
		$query = LiveRoom::where(['weid' => $weid]);
		if (!empty($keyword)) {
			$query->where('name', 'like', '%' . $keyword . '%');
		}

		$res = $query->order('sort asc,id asc')
			->paginate(getpage())
			->toArray();

		foreach ($res['data'] as &$vo) {
			$vo['anchor'] = LiveAnchor::getAnchor($vo['anchor_id']);
			$vo['start_time'] = time_format($vo['start_time']);
		}

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		LiveRoom::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$id = $this->request->post('id');
		$data = input('post.');
		unset($data['create_time']);

		$data['end_time'] = strtotime($data['start_time'].' +20 hour');
		$data['start_time'] = strtotime($data['start_time']);

		if (empty($data['room_id'])) {

			$LiveAnchor = LiveAnchor::getAnchor($data['anchor_id']);

			$createRoom = [
				'startTime' => $data['start_time'],
				'endTime' => is_string($data['end_time']) ? strtotime($data['end_time']) : $data['end_time'],
				'name' => $data['name'],
				'anchorName' => $LiveAnchor['name'],
				'anchorWechat' => $LiveAnchor['wechat'],
				'screenType' => $data['screen_type'],
				'closeGoods' => $data['close_goods'] == 1 ? 0 : 1,
				'closeLike' => $data['close_like'] == 1 ? 0 : 1,
				'closeComment' => $data['close_comment'] == 1 ? 0 : 1,
				'closeReplay' => $data['replay_status'] == 1 ? 0 : 1,
				'type' => $data['type'],
				'coverImg' => $data['cover_img'],
				'shareImg' => $data['share_img'],
				'closekf' => 1
			];

			$createRoomres = Author()::createLiveRoom($createRoom);
			if(!empty($createRoomres['roomId'])){
				$data['room_id'] = $createRoomres['roomId'];
			}else{
				throw new ValidateException($createRoomres);
			}
			
		}

		if (empty($id)) {
			$data['weid'] = weid();
			try {
				$res = LiveRoom::create($data);
				if ($res->id && empty($data['sort'])) {
					LiveRoom::update(['sort' => $res->id, 'id' => $res->id]);
				}
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage());
			}

			return $this->json(['msg' => '添加成功', 'data' => $res->id]);
		} else {

			try {
				LiveRoom::update($data);
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
		$data = LiveRoom::field('*')->find($id)->toArray();
		$data['start_time'] = time_format($data['start_time']);
		
		return $this->json(['data' => $data]);
	}

	function delete()
	{
		return $this->del(new LiveRoom());
	}
	function getField()
	{
		$data['anchorarray'] = LiveAnchor::getpagearray();

		return $this->json(['data' => $data]);
	}
}
