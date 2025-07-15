<?php

//七牛云oss上传

namespace utils\oss;
use think\facade\Log;

class QnyOssService
{
	
	/**
	 * @param  array $tempFile 本地图片路径
	 * @return string 图片上传返回的url地址
	 */
	public static function upload($oss_settings,$tmpInfo){
		
		$auth = new \Qiniu\Auth(trim($oss_settings['oss_accessKey']), trim($oss_settings['oss_secretKey']));
		$upToken = $auth->uploadToken(trim($oss_settings['oss_bucket']));
		$uploadMgr = new \Qiniu\Storage\UploadManager();
		try{
			$ret = $uploadMgr->putFile($upToken, \utils\oss\OssService::setKey('02',$tmpInfo),$tmpInfo['tmp_name']);
		}catch(\Exception $e){
			log::error('七牛云oss错误：'.print_r($e->getMessage(),true));
			throw new \Exception('上传失败');
		}
		//var_dump($ret);
		return trim($oss_settings['oss_domain']).'/'.$ret[0]['key'];
	}
    
}
