<?php

namespace utils\oss;

use Qcloud\Cos\Client;
use Qcloud\Cos\Exception\ServiceResponseException;
use think\facade\Log;

/**
 * 腾讯云存储 (COS)
 */
class QcloudOssService
{
    public static function upload($oss_settings, $tmpInfo)
    {
        $array = array(
            'region' => trim($oss_settings['region']),
            'schema' => 'https', //协议头部，默认为http
            'credentials' => array(
                'secretId'  => trim($oss_settings['oss_accessKey']),
                'secretKey' => trim($oss_settings['oss_secretKey']),
            )
        );
        $cosClient =  new Client($array);

        try {

            $file = fopen($tmpInfo['tmp_name'], "rb");

            if ($file) {
                try {
                    $result = $cosClient->putObject(
                        array(
                            'Bucket' => trim($oss_settings['oss_bucket']),
                            'Key' => \utils\oss\OssService::setKey('02', $tmpInfo),
                            'Body' => $file, //文件路径
                        )
                    );
                    return 'https://' . $result['Location'];
                } catch (ServiceResponseException $e) {
                    log::error('腾讯云oss错误：' . print_r($e->getMessage(), true));
                    throw new \Exception('上传失败');
                }
            } else {
                log::error('非法文件');
                throw new \Exception('上传失败');
            }
        } catch (\Exception $e) {
            log::error('腾讯云oss错误：' . print_r($e->getMessage(), true));
            throw new \Exception('上传失败');
        }
    }
}
