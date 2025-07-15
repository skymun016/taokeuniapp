<?php

namespace app\model;

use think\Model;

class Openid extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'openid';
	public static function addupdate($updata)
	{
		$weid = weid();
		$openid = $updata['openid'];
		if (empty($openid)) {
			$openid = getFans()['openid'];
		}

		$where['weid'] = $weid;
		if (!empty($updata['uid'])) {
			$where['uid'] = $updata['uid'];
		}

		$where['openid'] = $openid;

		if (!empty($updata['ptype'])) {
			$where['ptype'] = $updata['ptype'];
		}

		$Openidmob = self::where($where)->find();
		if ($Openidmob) {
			Openid::where(['id' => $Openidmob->id])->delete();
		}

		$adddata['weid'] = $weid;
		$adddata['uid'] = $updata['uid'];
		$adddata['ptype'] = $updata['ptype'];
		$adddata['openid'] = $openid;

		Openid::create($adddata);
	}
	public static function getMpOpenidbyuid($uid)
	{
		$data = self::where(['weid' => weid(), 'uid' => $uid, 'ptype' => 'mp'])->order('id desc')->find();
		if (!empty($data)) {
			return $data->openid;
		}
	}
	public static function getMpOpenidbyuuid($uuid)
	{
		if (!empty($uuid)) {
			if (empty($openid)) {
				$uid = UuidRelation::getuid($uuid);
				if (!empty($uid)) {
					$openid =  self::getMpOpenidbyuid($uid);
				}
			}

			return $openid;
		}
	}
	public static function getWxappOpenidbyuid($uid)
	{
		$data = self::where(['weid' => weid(), 'uid' => $uid, 'ptype' => 'wxapp'])->order('id desc')->find();
		if (!empty($data)) {
			return $data->openid;
		}
	}
	public static function getuidbyopenid($openid = '')
	{
		self::getuserbyopenid($openid)['uid'];
	}
	public static function getuserbyopenid($openid = '')
	{
		if (empty($openid)) {
			$openid = getFans()['openid'];
		}
		$query = self::where(['weid' => weid(), 'openid' => $openid]);
		$query->where('uid', '>', 0);
		$mob = $query->order('id desc')->find();
		if (!empty($mob)) {
			if (empty($mob->ptype)) {
				$mob->ptype = getFans()['ptype'];
				$mob->save();
			}
			return $mob->toArray();
		}
	}
}
