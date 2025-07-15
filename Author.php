<?php

namespace utils\core;

use think\facade\Request;
use app\model\Authorization;
use app\model\UsersSessions;
use app\model\UuidRelation;
use app\model\Member;
use EasyWeChat\Factory;
use app\model\Config;
use app\model\Paymethod;

class Author
{
    
    static function getlan()
	{
		return true;
	}
	
	static function setdiymodulebase($vo)
	{

		if ($vo['base']['bg'] == 1) {
			$vo['base']['bgstyle'] = 'background:' . $vo['base']['bc'] . ';';
		} elseif ($vo['base']['bg'] == 2) {
			$vo['base']['bgstyle'] = 'background:none;';
		} elseif ($vo['base']['bg'] == 3) {
			if ($vo['base']['bt'] == 1) {
				$vo['base']['bgstyle'] = 'background:' . $vo['base']['bc'] . ';';
			} else if ($vo['base']['bt'] == 2 && $vo['base']['bi'] != '') {
				$vo['base']['bgstyle'] = 'background:url(' . $vo['base']['bi'] . ') no-repeat 0px 0px;background-size:100%;';
			}
		}
		$vo['lan'] = self::getlan();
		return $vo;
	}
	
		static function setdiymodulecolumn($vo, $Configdata)
	{

		if ($vo['base']['column'] == 2) {
			if ($Configdata['listimgproportion'] == 43) {
				$vo['base']['widthheight'] = 'height: 253rpx;';
			} else {
				$vo['base']['widthheight'] = 'height: 337rpx;';
			}
		}
		if ($vo['base']['column'] == 3) {
			if ($Configdata['listimgproportion'] == 43) {
				$vo['base']['widthheight'] = 'height: 165rpx;';
			} else {
				$vo['base']['widthheight'] = 'height: 220rpx;';
			}
		}

		$vo['lan'] = self::getlan();
		return $vo;
	}
	static function setdiymoduletechcolumn($vo, $Configdata)
	{

		if ($vo['base']['column'] == 2) {

			if ($Configdata['techlistimgproportion'] == 43) {
				$vo['base']['widthheight'] = 'height: 253rpx;';
			} else {
				$vo['base']['widthheight'] = 'height: 337rpx;';
			}
		}
		if ($vo['base']['column'] == 3) {
			if ($Configdata['techlistimgproportion'] == 43) {
				$vo['base']['widthheight'] = 'height: 165rpx;';
			} else {
				$vo['base']['widthheight'] = 'height: 220rpx;';
			}
		}
		$vo['lan'] = self::getlan();
		return $vo;
	}
	
		/**
	 * 获取客户端IP地址
	 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
	 * @return mixed
	 */
	static function get_client_ip($type = 0, $adv = false)
	{
		$type = $type ? 1 : 0;
		static $ip = NULL;
		if ($ip !== NULL)
			return $ip[$type];
		if ($adv) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$pos = array_search('unknown', $arr);
				if (false !== $pos)
					unset($arr[$pos]);
				$ip = trim($arr[0]);
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (isset($_SERVER['REMOTE_ADDR'])) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u", ip2long($ip));
		$ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$type];
	}
	static function getdomainname()
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];

		if (empty($host)) {
			$host =  $_SERVER['HTTP_X_FORWARDED_SERVER'];
		}

		if (empty($host)) {
			$host = $_SERVER['HTTP_HOST'];
		}

		if (empty($host)) {
			$host =  $_SERVER["SERVER_NAME"];
		}
		return $host;
	}
    
    
}