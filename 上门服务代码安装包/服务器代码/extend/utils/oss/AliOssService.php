<?php

//阿里云oss上传

namespace utils\oss;
use think\facade\Log;
use OSS\OssClient;
use OSS\Core\OssException;

class AliOssService
{
	
	/**
	 * 阿里云oss上传
	 * @param  array $tempFile 本地图片路径
	 * @return string 图片上传返回的url地址
	 */
	public static function upload($oss_settings,$tmpInfo){
		try {
			$isCName = strpos(trim($oss_settings['oss_domain']),'aliyuncs.com') > 0 ? false : true;
			$ossClient = new OssClient(trim($oss_settings['oss_accessKey']), trim($oss_settings['oss_secretKey']),trim($oss_settings['oss_domain']),$isCName);
			$result = $ossClient->uploadFile(trim($oss_settings['oss_bucket']),\utils\oss\OssService::setKey('01',$tmpInfo),$tmpInfo['tmp_name']);
		} catch (OssException $e) {
			log::error('阿里oss错误：'.print_r($e->getMessage(),true));
			throw new \Exception('上传失败');
		}
		return $result['info']['url'];
	}
	
}
