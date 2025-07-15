<?php

namespace app\index\controller\kefu;

use think\exception\ValidateException;
use app\model\kefu\Chat;
use app\model\kefu\Seating;
use app\model\kefu\Seatinggroups;
use app\model\kefu\Contacts;

class ChatController extends Base
{

	function uslist()
	{
		$where['status'] = 1;
		$field = 'id,title,chatid';
		$query = Seating::where($where);
		$res = $query->field($field)
			->order('id desc')
			->select()
			->toArray();

		$res2 = Contacts::where($where)->field($field)
			->order('id desc')
			->select()
			->toArray();

		$data['data'] = array_merge($res, $res2);
		return $this->json($data);
	}

	function add()
	{
		$ChatId = input('post.ChatId', '', 'serach_in');
		$fromid = input('post.fromid', '', 'serach_in');
		if (!empty($ChatId)) {
			$ChatId = implode(',', $ChatId);
		}
		$chat = Chat::create(['weid' => weid(), 'title' => '群聊', 'is_group' => 1, 'crowd' => $ChatId, 'time' => time()]);
		$chat->title = $chat->title . $chat->id;
		Chat::update(['id' => $chat->id, 'title' => $chat->title]);

		$grouptopid = $chat->crowd;
		$grouptopid = str_replace($fromid . ',', "", $grouptopid);
		$grouptopid = str_replace(',' . $fromid, "", $grouptopid);
		$grouptopid = str_replace($fromid, "", $grouptopid);
		if(!empty($grouptopid)){
			$grouptopid = explode(",", $grouptopid);
		}

		$chat = $chat->toArray();
		$chat['grouptopid'] = $grouptopid;

		return $this->json($chat);
	}
}
