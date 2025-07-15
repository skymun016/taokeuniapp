<?php
declare (strict_types=1);

namespace app\samos\http\middleware;

use think\middleware\AllowCrossDomain;

use Closure;

/**
 * 自定义跨域中间件
 * @package app\samos\http\middleware
 */
class SamosAllowMiddleware extends AllowCrossDomain
{

    // 加入自定义请求头参数
    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Sec-Fetch-Mode, DNT, X-Mx-ReqToken, Keep-Alive, User-Agent, If-Match, If-None-Match, If-Unmodified-Since, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type, Accept-Language, Origin, Accept-Encoding,Access-Token,token,version',
    ];
}