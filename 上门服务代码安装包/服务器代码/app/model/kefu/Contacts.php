<?php

namespace app\model\kefu;

use think\Model;

class Contacts extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'kefu_contacts';

	public static function updataChat($data)
	{
		$weid = weid();
		$kefuconfig = \app\model\Config::getconfig('kefu');
		$contacts =	self::where(['weid' => $weid, 'chatid' => $data['chatid']])->find();
		if (empty($contacts)) {
			$data['weid'] = $weid;
			$data['title'] = empty($data['title']) ? '匿名用户' : $data['title'];
			$data['fromavatar'] = empty($data['fromavatar']) ? $kefuconfig['defaultavatar'] : $data['fromavatar'];
			$data['status'] = 1;
			$xi = self::create($data);

			self::update(['id'=>$xi->id,'title'=>$xi->title.$xi->id]);

		} else {

			if (!empty($data)) {
				self::update($data);
			}
		}
	}
}
