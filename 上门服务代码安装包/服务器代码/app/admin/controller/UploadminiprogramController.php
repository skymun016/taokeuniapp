<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use think\facade\Cache;
use app\model\Config;
use app\model\Uploadminiprogram;

class UploadminiprogramController extends Base
{

	function index()
	{
		$weid = weid();
		$path = input('post.path', '', 'serach_in');

		if ($path == "/config/uploadminiprogram") {
			$ptype = 1;
		} elseif (($path == "/config/techuploadminiprogram")) {
			$ptype = 2;
		}
		$query = Uploadminiprogram::where(['weid' => $weid, 'is_up' => 1, 'ptype' => $ptype]);

		$res = $query->order('id desc')
			->paginate(getpage())
			->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}

	function listUpdate()
	{
		$data = only('id,status,sort');
		if (!$data['id']) throw new ValidateException('参数错误');
		Uploadminiprogram::update($data);

		return $this->json(['msg' => '操作成功']);
	}

	public function update()
	{
		$postdata = $this->request->post();
		//$updata = $this->uploadWinpro($postdata);
		$updata = $this->uploadmini($postdata);

		if ($updata['data']['code'] == 2000) {
			$postdata['is_up'] = Uploadminiprogram::where('id', $postdata['id'])->update(['is_up' => 1]);
		}

		return $this->json(['msg' => $updata['data']['msg'], 'data' => $postdata]);
	}

	function getInfo()
	{
		$path = input('post.path', '', 'serach_in');

		if ($path == "/config/uploadminiprogram") {
			$ptype = 1;
		} elseif (($path == "/config/techuploadminiprogram")) {
			$ptype = 2;
		}

		$res = Uploadminiprogram::getnewversion($ptype);

		return $this->json(['data' => $res]);
	}
	/**
	 * @title 上传微信小程序
	 */
	public function uploadmini($postdata)
	{
		$weid = weid();
		$wechatConfig = Config::getconfig('miniprogram');
		if ($postdata['ptype'] == 2) {
			$multiid = '1';
			$wechatConfig['app_id'] = $wechatConfig['techapp_id'];
			$wechatConfig['secret'] = $wechatConfig['techsecret'];
			$wechatConfig['private_key'] = $wechatConfig['techprivate_key'];
		} else {
			$multiid = '0';
		}

		if ($wechatConfig) {
			if (!$wechatConfig['app_id']) throw new ValidateException('请先配置小程序appId');
			if (!$wechatConfig['secret']) throw new ValidateException('请先配置小程序appSecret');
			if (!$wechatConfig['private_key'])  throw new ValidateException('请先配置小程序上传密钥');
		} else {
			throw new ValidateException('请先配置小程序参数');
		}

		$domain = Author()::getdomainname();


		$configFiles = [
			[
				'name' => 'siteinfo.js',
				'path' => '/',
				'content' => 'var siteinfo = {"uniacid": "' . $weid . '","acid": "' . $weid . '","multiid": "' . $multiid . '","version": "' . $postdata['id'] . '","siteroot": "https://' . $domain . '/app/index.php","design_method": "3"};module.exports = siteinfo;'
			],

		];
		$url = 'http://console.samcms.com/public/index.php/index/authorization/uploadminiprogram';

		$post = [
			'appid' => trim($wechatConfig['app_id']),
			'wxapp_private_key' => trim($wechatConfig['private_key']),
			'version' => $postdata['version'],
			'desc' => $postdata['desctext'],
			'config_files' => json_encode($configFiles)
		];
		$authorization = Author()::authorizationInfo();
		$post['secret'] = trim($authorization['secret']);
		$post['seed'] = trim($authorization['seed']);
		$post['biaoshi'] = config('database.app_name');

		$result = vit_http_request($url, $post);
		$result = json_decode($result, true);
		//var_dump($result);
		if (!$result || $result['code'] != 2000) {
			throw new ValidateException($result['msg'] ?? '请求错误.');
		}
		$result['msg'] = $result['message'] ?? '上传成功';
		return ['data' => $result];
	}


	//荫析小程序上传开始

	/**
	 * @title 上传微信小程序
	 */
	public function uploadwinpro($postdata)
	{
		$weid = weid();
		$code = $this->getSiteCode();
		$wechatConfig = Config::getconfig('miniprogram');
		if ($postdata['ptype'] == 2) {
			$multiid = '1';
			$wechatConfig['app_id'] = $wechatConfig['techapp_id'];
			$wechatConfig['secret'] = $wechatConfig['techsecret'];
			$wechatConfig['private_key'] = $wechatConfig['techprivate_key'];
		} else {
			$multiid = '0';
		}

		if (!$code) throw new ValidateException('请先配置在线更新');

		$pid = input('pid');

		if ($wechatConfig) {
			if (!$wechatConfig['app_id']) throw new ValidateException('请先配置小程序appId');
			if (!$wechatConfig['secret']) throw new ValidateException('请先配置小程序appSecret');
			if (!$wechatConfig['private_key'])  throw new ValidateException('请先配置小程序上传密钥');
		} else {
			throw new ValidateException('请先配置小程序参数');
		}

		$domain = Author()::getdomainname();


		$configFiles = [
			[
				'name' => 'siteinfo.js',
				'path' => '/',
				'content' => 'var siteinfo = {"uniacid": "' . $weid . '","acid": "' . $weid . '","multiid": "' . $multiid . '","version": "' . $postdata['id'] . '","siteroot": "https://' . $domain . '/app/index.php","design_method": "3"};module.exports = siteinfo;'
			],

		];
		$url = 'https://www.mzapp.cn/api/wxapp';
		$post = [
			'code' => $code,
			'goods' => getmzgoodsid(),
			'pid' => $pid,
			'appid' => trim($wechatConfig['app_id']),
			'wxapp_private_key' => trim($wechatConfig['private_key']),
			'version' => $postdata['version'],
			'desc' => $postdata['desctext'],
			'config_files' => json_encode($configFiles)
		];
		$result = vit_http_request($url, $post);
		$result = is_null(json_decode($result)) ? [] : json_decode($result, true);
		if (!$result || $result['code'] != 1) {
			return  $result['msg'] ?? '请求错误.';
		}

		return $this->queryUpload($result['data']['order_no']);
	}

	public function queryUpload($order)
	{
		if (empty($order)) {
			return;
		}
		$url = 'https://www.mzapp.cn/api/wxapp/upload';
		$post = [
			'code' => $this->getSiteCode(),
			'order' => $order
		];
		$result = vit_http_request($url, $post);
		$result = is_null(json_decode($result)) ? [] : json_decode($result, true);
		if (!$result || $result['code'] != 1) {
			return $result['code'];
		}
		return $result;
	}

	/**
	 * 获取站点code
	 */
	public function getSiteCode($refresh = false)
	{
		if ($refresh) {
			return $this->getRefreshSiteCode();
		}

		$code = Cache::get('vit_site_code');
		if (!$code) {
			return $this->getRefreshSiteCode();
		}
		return $code;
	}

	/**
	 * 重新获取新的站点code值
	 */
	private function getRefreshSiteCode()
	{
		$url = 'http://console.samcms.com/public/index.php/index/authorization/getrefreshsitecode';
		$postparam = Author()::authorizationInfo();
		$postparam['biaoshi'] = config('database.app_name');
		$result = vit_http_request($url, $postparam);

		$result = is_null(json_decode($result)) ? [] : json_decode($result, true);

		if (($result['data']['appid'])) {
			$Configdata = $result['data'];
		} else {
			throw new ValidateException($result['msg']);
		}

		if ($Configdata['appid']) {
			$appid = $Configdata['appid'];
			$appkey = $Configdata['appkey'];
			$time = time();
			$sign = sha1($appid . $appkey . $Configdata['domain'] . $time);

			$url = 'https://www.mzapp.cn/api/code/get';
			$post = [
				'domain' => $Configdata['domain'],
				'appid' => $appid,
				'sign' => $sign,
				'time' => $time
			];
			$result = vit_http_request($url, $post);
			$result = is_null(json_decode($result)) ? [] : json_decode($result, true);
			if (!$result || $result['code'] != 1) {
				return '';
			}
			Cache::set('vit_site_code', $result['data']['code'], '600');
		}

		return $result['data']['code'];
	}
	//荫析小程序上传结束
}
