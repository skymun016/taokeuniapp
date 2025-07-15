<?php

namespace app\model;

use think\Model;

class Member extends Model
{

	protected $connection = 'mysql';

	protected $pk = 'id';

	protected $name = 'member';

	function setmemberinfo($member)
	{
		if (!empty($member)) {
			$member['gname'] = MemberAuthGroup::getGroupinfo($member['gid'])['title'];
			$member['pname'] = Member::getpidname($member['id']);
		}
		return $member;
	}

	public static function conversion($vo)
	{
		$vo['gid'] = MemberAuthGroup::getgroupName($vo['gid']);
		$vo['pid'] = Member::get_name($vo['pid']) ?? '平台';
		$vo['regdate'] = time_format($vo['regdate']);
		$vo['lastdate'] = time_format($vo['lastdate']);

		if (!empty($vo['uuid'])) {
			$vo['username'] = Users::getusername($vo['uuid']);
		}
		$vo['region_name'] = $vo['province_name'] . $vo['city_name'] . $vo['district_name'];
		return $vo;
	}

	function getUserbyopenid()
	{

		if (!empty(getFans()['openid'])) {

			if (empty($userinfo)) {
				$user = Openid::getuserbyopenid();
				$uid = $user['uid'];
				if (!empty($uid)) {
					$userinfo = $this::where(['id' => $uid, 'weid' => weid()])
						->order('id desc')
						->find();
				}
			}
			if (!empty($userinfo)) {
				$userinfo = $userinfo->toArray();
			}
		}

		unset($userinfo["password"]);
		return $userinfo;
	}

	function getUserByWechat()
	{

		$userinfo = $this->getUserbyopenid();

		if (empty($userinfo["id"])) {
			$this->wechatReg();
			$userinfo = $this->getUserbyopenid();
		}

		return $this->setmemberinfo($userinfo);
	}

	function wechatReg()
	{

		if (!empty(getFans()['openid'])) {

			$userinfo = $this->getUserbyopenid();

			if (empty($userinfo)) {

				$member = $this->getmemberdata();
				$member['category_id'] = 0;

				$Configmember = Config::getconfig('member');

				if ($Configmember['reg_check'] == 1) {
					$member['status'] = 0;
				} else {
					$member['status'] = 1;
				}

				$agent = Config::getconfig('agent');
				if ($agent['share_condition'] == 1) {
					Agent::register($member);
				}

				$res = $this::create($member);

				if ($res->id && $res->nickname == '用户') {
					self::update(['nickname' => $res->nickname . '_' . $res->id, 'id' => $res->id]);
				}

				if (!empty($res)) {
					$odate['weid'] = weid();
					$odate['uid'] = $res->id;
					$odate['openid'] = getFans()['openid'];
					$odate['ptype'] = getFans()['ptype'];
					Openid::addupdate($odate);
				}
			}
		}
	}

	public static function phoneReg($phone)
	{
		$member = (new self());
		$member = $member->getmemberdata();
		$member['telephone'] = $phone;
		$member['category_id'] = 0;

		$Configmember = Config::getconfig('member');

		if ($Configmember['reg_check'] == 1) {
			$member['status'] = 0;
		} else {
			$member['status'] = 1;
		}

		$agent = Config::getconfig('agent');
		if ($agent['share_condition'] == 1) {
			Agent::register($member);
		}

		$res = self::create($member);

		if ($res->id && $res->nickname == '用户') {
			self::update(['nickname' => $res->nickname . '_' . $res->id, 'id' => $res->id]);
		}

		if (!empty(getFans()['openid']) && !empty($res)) {
			$odate['weid'] = weid();
			$odate['uid'] = $res->id;
			$odate['openid'] = getFans()['openid'];
			$odate['ptype'] = getFans()['ptype'];
			Openid::addupdate($odate);
		}
		if (!empty($res)) {
			$res = $res->toArray();
		}
		return $res;
	}

	function getmemberdata()
	{
		getFans()['tag']['nickname'] =  removeEmoji(getFans()['tag']['nickname']);
		$membergroup = MemberAuthGroup::getdefaultGroup();;
		$gid = $membergroup['id'];

		if (empty(getFans()['tag']['sex'])) {
			$member['sex'] = 0;
		} else {
			$member['sex'] = getFans()['tag']['sex'];
		}
		$member['weid'] = weid();
		$member['userpic'] = getFans()['tag']['avatar'];
		$member['reg_type'] = 'mobile';
		$member['gid'] = $gid;
		$member['regdate'] = time();
		$member['lastdate'] = time();
		//var_dump(getFans()['tag']['nickname']);
		if (empty(getFans()['tag']['nickname'])) {
			$member['nickname'] = '用户';
		} else {
			$member['nickname'] = getFans()['tag']['nickname'];
		}

		if (!empty(getFans()['tag']['country'])) {
			$member['country'] = getFans()['tag']['country'];
		}

		if (!empty(getFans()['tag']['province'])) {
			$member['province'] = getFans()['tag']['province'];
		}

		if (!empty(getFans()['tag']['city'])) {
			$member['city'] = getFans()['tag']['city'];
		}

		//$member['resume'] = iserializer($_W);
		return $member;
	}

	public static function bindphonenumber($phone)
	{
		if (empty($phone)) {
			return;
		}

		$member = self::where(['telephone' => $phone, 'weid' => weid()])->find();
		if (!empty($member)) {
			$member = $member->toArray();
			Openid::addupdate(['uid' => $member['id'], 'openid' => $phone, 'ptype' => getFans()['ptype']]);

			if (!empty(getFans()['openid'])) {
				Openid::addupdate(['uid' => $member['id'], 'openid' => getFans()['openid'], 'ptype' => getFans()['ptype']]);
			}
		} else {
			$member = (new self())->getUserbyopenid();
			if (!empty($member["id"])) {
				self::where('id', $member["id"])->update(['telephone' => $phone]);
				$member["telephone"] = $phone;
			} else {
				$member = self::phoneReg($phone);
			}
		}
		return $member;
	}

	public static function get_name($uid)
	{
		$ret = self::find($uid);
		return $ret->nickname;
	}

	public static function getpidname($uid)
	{
		$member = self::find($uid);

		if ($member->pid) {
			$memberpid = self::find($member->pid);
			return $memberpid->nickname;
		} else {
			return '平台';
		}
	}

	public static function getDepart($pid)
	{
		$uidlist = self::where(['pid' => $pid])->select()->toArray();

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

	public static function getonelevel($uid, $isdata = false)
	{
		if ($isdata) {
			return self::field('id,nickname,regdate,userpic')->where(['weid' => weid(), 'pid' => $uid])->select()->toArray();
		} else {
			return self::where(['weid' => weid(), 'pid' => $uid])->count();
		}
		return self::where(['weid' => weid(), 'pid' => $uid])->count();
	}

	public static function gettwolevel($uid, $isdata = false)
	{
		$data['first'] = self::field('id,nickname')->where(['weid' => weid(), 'pid' => $uid])->select()->toArray();
		if (!empty($data['first'])) {
			foreach ($data['first'] as $vo) {
				if (empty($pid)) {
					$pid = $vo['id'];
				} else {
					$pid = $pid . ',' . $vo['id'];
				}
			}
			if (!empty($pid)) {
				$pidin = explode(',', $pid);
			}

			if ($isdata) {
				return self::field('id,nickname,regdate,userpic')->where(['weid' => weid(), 'pid' => $pidin])->select()->toArray();
			} else {
				return self::where(['weid' => weid(), 'pid' => $pidin])->count();
			}
		} else {
			return 0;
		}
	}

	public static function getthreelevel($uid, $isdata = false)
	{
		$data['first'] = self::field('id,nickname')->where(['weid' => weid(), 'pid' => $uid])->select()->toArray();
		if (!empty($data['first'])) {
			foreach ($data['first'] as $vo) {
				if (empty($pid)) {
					$pid = $vo['id'];
				} else {
					$pid = $pid . ',' . $vo['id'];
				}
			}
			if (!empty($pid)) {
				$pidin = explode(',', $pid);
			}

			$data['first'] = self::field('id,nickname')->where(['weid' => weid(), 'pid' => $pidin])->select()->toArray();

			if (!empty($data['second'])) {
				foreach ($data['second'] as $so) {
					if (empty($tpid)) {
						$tpid = $so['id'];
					} else {
						$tpid = $tpid . ',' . $so['id'];
					}
				}
				if (!empty($tpid)) {
					$tpidin = explode(',', $tpid);
				}
				if ($isdata) {
					return self::field('id,nickname,regdate,userpic')->where(['weid' => weid(), 'pid' => $tpidin])->select()->toArray();
				} else {
					return self::where(['weid' => weid(), 'pid' => $tpidin])->count();
				}
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	//判断手机号是否已注册
	public function is_register($telephone)
	{
		$is_register = self::where(['telephone' => $telephone])->find();
		if (!empty($is_register)) {
			$is_register = $is_register->toArray();
		}

		if (empty($is_register)) {
			return true;
		} else
			return false;
	}
	//检查是否审核通过!
	public function is_check($uid)
	{
		$result = self::where(['status' => 1, 'id' => $uid])->find();
		if (!empty($result)) {
			return true;
		} else
			return false;
	}
	public function checked($telphone)
	{
		$result = self::where(['telephone' => $telphone])->find();
		if (!empty($result)) {
			return true;
		} else
			return false;
	}
	public function checkAgent($uid)
	{

		$result = Agent::is_agent($uid);
		if (empty($result)) {
			exit(\json_encode('错误，请先成为分销商!'));
		}
	}
}
