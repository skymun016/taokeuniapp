<?php

namespace app\model;

use think\Model;

class Users extends Model
{


	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'user';

	function roles()
	{
		return $this->hasOne(UsersRoles::class, 'id', 'role_id');
	}

	public static function getusername($uuid)
	{
		$user = self::where('uuid', $uuid)->find();
		return $user->username;
	}

	public static function getuserbyuuid($uuid)
	{
		$user = self::where('uuid', $uuid)->find();

		if (!empty($user)) {
			$user = $user->toArray();
		}
		return $user;
	}

	public static function getDepart($did)
	{

		$uidlist =	self::field('id,username')->where(['did' => $did])
			->order('id asc')
			->select()
			->toArray();

		foreach ($uidlist as $vo) {
			if (empty($uidliststr)) {
				$uidliststr = $vo['id'];
			} else {
				$uidliststr .= ',' . $vo['id'];
			}
		}
		if (!empty($uidliststr)) {
			return explode(',', $uidliststr);
		}
	}

	public static function getuserbyuid($uid)
	{
		$userarray = self::where(['uid' => $uid, 'weid' => weid()])->find();
		if ($userarray) {
			$userarray = $userarray->toArray();
		}

		return $userarray;
	}
	public static function getadminbyopenid()
	{

		if (!empty(getFans()['openid'])) {

			$uuid = UuidRelation::getuuid(UID(), 'admin');
			if (!empty($uuid)) {
				$userarray = self::where(['uuid' => $uuid, 'weid' => weid()])->find();

				if ($userarray) {
					$userarray = $userarray->toArray();
					$UsersRoles = UsersRoles::getinfo($userarray);
					$userarray['UsersRoles'] = $UsersRoles;
				}
			}
		}
		return $userarray;
	}

	public static function getallarray()
	{
		$list =	self::field('id,username')
			->order('id asc')
			->select()
			->toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k]['val'] = $v['id'];
			$array[$k]['key'] = $v['username'];
		}
		return $array;
	}
}
