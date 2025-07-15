<?php

namespace app\model;

class Geocoder

{

	//百度
	static function getbaiduak()
	{
		$data = Config::getconfig('lbsapi');

		if (empty($data['baiduak'])) {
			$data = Config::getsitesetupconfig('lbsapi');
		}
		return $data['baiduak'];
	}
	//腾讯
	static function getqqak()
	{
		$data = Config::getconfig('lbsapi');

		if (empty($data['qqak'])) {
			$data = Config::getsitesetupconfig('lbsapi');
		}
		return $data['qqak'];
	}

	static function geocoding($address)
	{
		$address = str_replace("#", "", $address);
		$url = 'https://api.map.baidu.com/geocoding/v3/?address=' . $address . '&output=json&ak=' . self::getbaiduak() . '&callback=showLocation';
		$ret = urlget($url);
		$ret = str_replace('showLocation&&showLocation(', '', $ret);
		$ret = str_replace(')', '', $ret);
		$ret = json_decode($ret, true);

		if (!empty($ret) && $ret['status'] == 0) {
			$result['latitude'] = $ret['result']['location']['lat'];
			$result['longitude'] = $ret['result']['location']['lng'];
			return $result; //返回经纬度结果
		} else {
			return null;
		}
	}
	static function reverse_geocoding($latitude, $longitude)
	{
		$ret =  self::baidu_reverse_geocoding($latitude, $longitude);
		//$ret =  self::baidu_reverse_geocoding('22.91824108097', '110.9952491133');

		if (empty($ret)) {
			$ret =	self::qq_reverse_geocoding($latitude, $longitude);
			$ret['addressComponent'] = $ret['address_component'];
		}

		if (!empty($ret)) {
			return $ret; //返回经纬度结果
		} else {
			return null;
		}
	}
	//百度
	static function baidu_reverse_geocoding($latitude, $longitude)
	{
		$url = 'https://api.map.baidu.com/reverse_geocoding/v3/?ak=' . self::getbaiduak() . '&output=json&coordtype=wgs84ll&location=' . $latitude . ',' . $longitude;
		$ret = urlget($url);
		$ret = str_replace('showLocation&&showLocation(', '', $ret);
		$ret = str_replace(')', '', $ret);
		$ret = json_decode($ret, true);
		if (!empty($ret) && $ret['status'] == 0) {
			return $ret['result']; //返回经纬度结果
		} else {
			return null;
		}
	}

	//腾讯
	static function qq_reverse_geocoding($latitude, $longitude)
	{
		$url = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . $latitude . ',' . $longitude . '&key=' . self::getqqak() . '&get_poi=1';
		$ret = urlget($url);
		$ret = str_replace('showLocation&&showLocation(', '', $ret);
		$ret = str_replace(')', '', $ret);
		$ret = json_decode($ret, true);

		if (!empty($ret) && $ret['status'] == 0) {
			return $ret['result']; //返回经纬度结果
		} else {
			return null;
		}
	}

	/**
	 * 根据起点坐标和终点坐标测距离
	 * @param  [array]   $from 	[起点坐标(经纬度),例如:array(118.012951,36.810024)]
	 * @param  [array]   $to 	[终点坐标(经纬度)]
	 * @param  [bool]    $km        是否以公里为单位 false:米 true:公里(千米)
	 * @param  [int]     $decimal   精度 保留小数位数
	 * @return [string]  距离数值
	 */
	static function get_distance($from, $to, $km = true, $decimal = 2)
	{
		sort($from);
		sort($to);
		$EARTH_RADIUS = 6370.996; // 地球半径系数

		$distance = $EARTH_RADIUS * 2 * asin(sqrt(pow(sin(($from[0] * pi() / 180 - $to[0] * pi() / 180) / 2), 2) + cos($from[0] * pi() / 180) * cos($to[0] * pi() / 180) * pow(sin(($from[1] * pi() / 180 - $to[1] * pi() / 180) / 2), 2))) * 1000;

		if ($km) {
			$distance = $distance / 1000;
		}

		return round($distance, $decimal);
	}
}
