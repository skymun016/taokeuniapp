<?php

namespace app\model\kefu;

use think\Model;

class Chat extends Model
{
	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'kefu_chat';

	public static function getchatid($fromid, $toid)
	{
		$weid = weid();
		$crowd = $fromid . ',' . $toid;
		$crowdor = $toid . ',' . $fromid;
		$chat = self::where(['weid' => $weid, 'crowd' => $crowd])->find();
		if (!empty($chat)) {
			return $chat->id;
		}

		$chat = self::where(['weid' => $weid, 'crowd' => $crowdor])->find();
		if (!empty($chat)) {
			return $chat->id;
		}

		$chat = self::create(['weid' => $weid, 'crowd' => $crowd, 'time' => time()]);

		return $chat->id;
	}

	public static function getcrowd($id)
	{
		$chat = self::where(['id' => $id])->find();
		return $chat->crowd;
	}

}
