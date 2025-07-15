<?php

namespace app\model;

use think\Model;

class UuidRelation extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'uuid_relation';

	public static function getuuid($uid, $ptype)
	{
		$query = self::where(['weid' => weid(), 'ptype' => $ptype, 'uid' => $uid])->whereNotNull('uuid')->where('uuid', '<>', '');
		$mob = $query->order('id desc')->find();

		if (!empty($mob)) {
			return $mob->uuid;
		}
	}

	public static function getuuidbyid($userInfo, $ptype)
	{
		if ($ptype == 'store' &&  $userInfo['sid']) {
			$store = Store::find($userInfo['sid']);
			if ($store) {
				if ($store->uuid) {
					$uuid = $store->uuid;
				} else {
					$uuid = uniqid(rand(1, 10000));
					Store::where('id', $userInfo['sid'])->update(['uuid' => $uuid]);
				}
			}
		}

		if ($ptype == 'tuanzhang' &&  $userInfo['tzid']) {
			$Tuanzhang = Tuanzhang::find($userInfo['tzid']);
			if ($Tuanzhang) {
				if ($Tuanzhang->uuid) {
					$uuid = $Tuanzhang->uuid;
				} else {
					$uuid = uniqid(rand(1, 10000));
					Tuanzhang::where('id', $userInfo['tzid'])->update(['uuid' => $uuid]);
				}
			}
		}

		if ($ptype == 'operatingcity' &&  $userInfo['ocid']) {
			$Operatingcity = Operatingcity::find($userInfo['ocid']);
			if ($Operatingcity) {
				if ($Operatingcity->uuid) {
					$uuid = $Operatingcity->uuid;
				} else {
					$uuid = uniqid(rand(1, 10000));
					Operatingcity::where('id', $userInfo['ocid'])->update(['uuid' => $uuid]);
				}
			}
		}

		if ($ptype == 'technical' &&  $userInfo['tid']) {
			$Technical = Technical::find($userInfo['tid']);
			if ($Technical) {
				if ($Technical->uuid) {
					$uuid = $Technical->uuid;
				} else {
					$uuid = uniqid(rand(1, 10000));
					Technical::where('id', $userInfo['tid'])->update(['uuid' => $uuid]);
				}
			}
		}

		if (empty($uuid)) {
			$uuid = $userInfo['uuid'];
		}

		return $uuid;
	}

	public static function getuid($uuid)
	{
		$query = self::where(['weid' => weid(), 'uuid' => $uuid]);
		$mob = $query->order('id desc')->find();

		if (!empty($mob)) {
			return $mob->uid;
		}
	}
}
