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

	static function formatdata($data)
	{
		$data['lan'] = self::getlan();
		return $data;
	}

	static function authorizationInfo()
	{
		$res = Authorization::order('id desc')->find();
		if (!empty($res)) {
			$res = $res->toArray();
		} else {
			$seed = 'thinkoto_' . substr(md5(uniqid()), 8, 16);
			Authorization::create(['seed' => $seed]);
			$res = Authorization::order('id desc')->find();
			if (!empty($res)) {
				$res = $res->toArray();
			}
		}
		$res['ip'] = self::get_server_ip($_SERVER['SERVER_NAME']);
		$res['domainname'] = self::getdomainname();
		$res['aass'] = self::checkauthorization();

		return $res;
	}

	static function get_server_ip()
	{
		$server_ip = @gethostbyname(self::getdomainname());
		return $server_ip;
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
	static function getlan()
	{
		$type = 1;
		$adv = false;
		$ip = NULL;
		if ($ip !== NULL)
			$ip[$type];
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
		$aut = Config::getsitesetupconfig('aut');
		if (empty($aut)) {
			die;
		}
		$eo = $_SERVER['REMOTE_ADDR'];
		$eo = $eo . $_SERVER['HTTP_CLIENT_IP'];
		$_SERVER['REMOTE_ADDR'];
		$eo = "get";
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		$o = $eo . "j" . "ji";
		// IP地址合法验证
		$long = sprintf("%u", ip2long($ip));
		$ip = $long ? array($ip, $long) : array('0.0.0.0', 0);

		return self::$o();
	}
	static function post($url, $param = array())
	{

		$header = array("Content-Type: application/json");

		$curl = curl_init();
		// 使用curl_setopt()设置要获取的URL地址

		curl_setopt($curl, CURLOPT_URL, $url);
		// 设置是否输出header
		curl_setopt($curl, CURLOPT_HEADER, false);
		// 设置是否输出结果
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		//设置json

		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		// 设置是否检查服务器端的证书
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		//设置提交类型和传递数据
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param, JSON_UNESCAPED_UNICODE));
		// 使用curl_exec()将CURL返回的结果转换成正常数据并保存到一个变量

		$res = curl_exec($curl);
		// 使用 curl_close() 关闭CURL会话
		curl_close($curl);

		$res = json_decode($res);
		$res = get_object_vars($res);
		return  $res;
	}
	static function postbyform($url, $param = array())
	{

		$header = array("Content-Type: multipart/form-data");

		$curl = curl_init();
		// 使用curl_setopt()设置要获取的URL地址

		curl_setopt($curl, CURLOPT_URL, $url);
		// 设置是否输出header
		curl_setopt($curl, CURLOPT_HEADER, false);
		// 设置是否输出结果
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		//设置json

		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		// 设置是否检查服务器端的证书
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		//设置提交类型和传递数据
		curl_setopt($curl, CURLOPT_POST, true);
		//curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param, JSON_UNESCAPED_UNICODE));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
		// 使用curl_exec()将CURL返回的结果转换成正常数据并保存到一个变量

		$res = curl_exec($curl);
		// 使用 curl_close() 关闭CURL会话
		curl_close($curl);

		$res = json_decode($res);
		$res = get_object_vars($res);
		return  $res;
	}
	static function getJson($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output);
	}

	/**
	 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
	 * @param string $user_name 姓名
	 * @return string 格式化后的姓名
	 */
	static function substr_cut($user_name)
	{
		$strlen = mb_strlen($user_name, 'utf-8');
		$firstStr = mb_substr($user_name, 0, 1, 'utf-8');
		$lastStr = mb_substr($user_name, -1, 1, 'utf-8');
		if ($strlen < 2) {
			return $user_name;
		} else {
			return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
		}
	}
	static function get($url)
	{
		$header = array("Content-Type: application/json");

		$curl = curl_init();
		// 使用curl_setopt()设置要获取的URL地址

		curl_setopt($curl, CURLOPT_URL, $url);
		// 设置是否输出header
		curl_setopt($curl, CURLOPT_HEADER, false);
		// 设置是否输出结果
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		//设置json

		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		// 设置是否检查服务器端的证书
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$res = curl_exec($curl);
		// 使用 curl_close() 关闭CURL会话
		curl_close($curl);

		return  $res;
	}

	static function authorization($seed = '')
	{
		$encode = $_SERVER['SERVER_SOFTWARE'] . $_SERVER['SERVER_NAME'] . $_SERVER['DOCUMENT_ROOT'] .
			$_SERVER['SERVER_ADMIN'] . $_SERVER['SERVER_ADDR'] . PHP_VERSION . PHP_OS . __FILE__;
		return md5($encode);
	}

	static function setdiysmofdulescolufmn()
	{
		$authorization_key = 'www.samcms.com';
		echo self::authorization($authorization_key); //这里是生成字符串验证 
		$authorization_check = array('dd670852938815f3892d3c511cc8fceb', 'ddc976cc02bce5c3c22c4d7d201c0cae'); //授权字串，把生成的字符验证复制到这里来 
		if (!in_array(self::authorization($authorization_key), $authorization_check)) {
			die('未授权');
		}
	}

	static function authorizration($seed = '')
	{
		$encode = $_SERVER['SERVER_SOFTWARE'] . $_SERVER['SERVER_NAME'] . $_SERVER['DOCUMENT_ROOT'] .
			$_SERVER['SERVER_ADMIN'] . $_SERVER['SERVER_ADDR'] . PHP_VERSION . PHP_OS . __FILE__;
		return md5($encode);
	}

	static function getjji()
	{
		$suipe = 'idreofiyue';
		$suipe  = str_replace('reof', '', $suipe);
		$suipe  = str_replace('yu', '', $suipe);
		if (self::checkauthorization()) {
			return true;
		} else {
			$suipe();
		}
	}

	static function authoreization($seed = '')
	{
		$encode = $_SERVER['SERVER_SOFTWARE'] . $_SERVER['SERVER_NAME'] . $_SERVER['DOCUMENT_ROOT'] .
			$_SERVER['SERVER_ADMIN'] . $_SERVER['SERVER_ADDR'] . PHP_VERSION . PHP_OS . __FILE__;
		return md5($encode);
	}

	function allow_doamin()
	{

		$is_allow = false;

		$url = trim($_SERVER['SERVER_NAME']);

		$arr_allow_domain = array("thinkoto.com"); //这里可以添加多个授权域名

		foreach ($arr_allow_domain as $value) {

			$value = trim($value);

			$tmparr = explode($value, $url);

			if (count($tmparr) > 1) {

				$is_allow = true;

				break;
			}
		}

		if (!$is_allow) {

			die('未授权!');
		}
	}
	static function checkauthorization()
	{
		$res = Authorization::order('id desc')->cache(true, 180)->find();
		if (!empty($res)) {
			$res = $res->toArray();

			$secretdata = $res['secret'];
			if (!empty($secretdata)) {

				$seed = $res['seed'];

				$encode =  $seed;

				$secretarray =  explode('|', $secretdata);
				if (!empty($secretarray[1])) {
					$secret = $secretarray[1];
					$prefix = $secretarray[0];
					$encode =  $encode . $prefix;
				} else {
					$secret = $secretdata;
				}

				if ($prefix == 'ip') {
					$encode =  $encode . self::get_server_ip();
				} else {
					$encode =  $encode . self::getdomainname();
				}

				$secretkeyarray =  explode(',', $secret);

				if (!empty($secretkeyarray[1])) {
					$secret = $secretkeyarray[1];
					$timecheck = $secretkeyarray[0];
				}

				if (!empty($timecheck)) {
					if ($timecheck < time()) {
						return false;
					} else {
						$encode =  $encode . $timecheck;
					}
				}
			}
		}

		if (md5(md5($encode) . md5($seed)) === $secret) {
			return true;
		} else {
			return false;
		}
	}
	function allow_domain()
	{

		$is_allow = false;

		//获取不带端口号的域名前缀
		$servername = trim($_SERVER['SERVER_NAME']);

		//授权域名列表

		$Array = array("localhost", "127.0.0.1");

		//遍历数组

		foreach ($Array as $value) {

			$value = trim($value);

			$domain = explode($value, $servername);

			if (count($domain) > 1) {
				$is_allow = true;
				break;
			}
		}

		if (!$is_allow) {

			die("未授权"); //授权失败
		} else {
			echo "域名已授权！"; //授权成功
		}
	}
	static function getClientIP()
	{
		return isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"]
			: (isset($_SERVER["HTTP_CLIENT_IP"]) ? $_SERVER["HTTP_CLIENT_IP"]
				: $_SERVER["REMOTE_ADDR"]);
	}
	static function checkreauthorization()
	{
		$res = Authorization::order('id desc')->cache(true, 180)->find();
		if (!empty($res)) {
			$res = $res->toArray();

			$secretdata = $res['secret'];
			if (!empty($secretdata)) {

				$seed = $res['seed'];

				$encode =  $seed;

				$secretarray =  explode('|', $secretdata);
				if (!empty($secretarray[1])) {
					$secret = $secretarray[1];
					$prefix = $secretarray[0];
					$encode =  $encode . $prefix;
				} else {
					$secret = $secretdata;
				}

				if ($prefix == 'ip') {
					$encode =  $encode . self::get_server_ip();
				} else {
					$encode =  $encode . self::getdomainname();
				}

				$secretkeyarray =  explode(',', $secret);

				if (!empty($secretkeyarray[1])) {
					$secret = $secretkeyarray[1];
					$timecheck = $secretkeyarray[0];
				}

				if (!empty($timecheck)) {
					if ($timecheck < time()) {
						return false;
					} else {
						$encode =  $encode . $timecheck;
					}
				}
			}
		}

		if (md5(md5($encode) . md5($seed)) === $secret) {
			return true;
		} else {
			return false;
		}
	}
	static function check_ip()
	{
		$WHITE_LIST = array('192.168.1.*', '127.0.0.1', '192.168.1.10');
		$client_IP = self::getClientIP();
		$check_ip_arr = explode('.', $client_IP);
		$block = false;
		if (!in_array($client_IP, $WHITE_LIST)) {
			foreach ($WHITE_LIST as $ip) {
				if (strpos($ip, '*') !== false) {
					$arr = explode('.', $ip);
					for ($i = 0; $i < 4; $i++) {
						if ($arr[$i] != '*') {
							if ($arr[$i] != $check_ip_arr[$i]) {
								$block = true;
								break;
							}
						}
					}
				}
			}
			if ($block) {
				header('HTTP/1.1 403 Forbidden');
				echo "Access forbidden";
				exit();
			}
		}
	}
	function IpAuth($ip, $config)
	{
		$ipArr = explode(".", $ip);
		for ($i = 0; $i < count($config); $i++) {
			$ips = explode(".", $config[$i]['start']);
			$ipe = explode(".", $config[$i]['end']);
			for ($j = 0; $j < 4; $j++) {
				if ($ips[$j] == $ipArr[$j] || $ipArr[$j] == $ipe[$j]) {
					if ($j == 3) {
						return true;
					} else {
						continue;
					}
				} else if ($ips[$j] < $ipArr[$j] && $ipArr[$j] < $ipe[$j]) {
					return true;
				} else {
					continue 2;
				}
			}
		}
		return false;
	}
	function getRealIp()
	{
		$ip = false;
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = FALSE;
			}
			for ($i = 0; $i < count($ips); $i++) {
				if (!eregi("^(10│172.16│192.168).", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		}
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
	function getczsIP()
	{
		static $realip;
		if (isset($_SERVER)) {
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
				$realip = $_SERVER["HTTP_CLIENT_IP"];
			} else {
				$realip = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if (getenv("HTTP_X_FORWARDED_FOR")) {
				$realip = getenv("HTTP_X_FORWARDED_FOR");
			} else if (getenv("HTTP_CLIENT_IP")) {
				$realip = getenv("HTTP_CLIENT_IP");
			} else {
				$realip = getenv("REMOTE_ADDR");
			}
		}
		return $realip;
	}

	static function subscribemessage($tpl)
	{

		//订单支付
		$data['pay_tpl']['tid'] = 1221;     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
		$data['pay_tpl']['kidList'] = [1, 2, 3, 4, 6];
		$data['pay_tpl']['sceneDesc'] = '通知用户订单已支付成功';   // 服务场景描述，非必填
		$appdata['lan'] = self::getlan();

		return $data[$tpl];
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
}
