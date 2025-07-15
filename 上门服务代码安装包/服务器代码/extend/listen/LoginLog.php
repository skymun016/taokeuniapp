<?php

//登录日志写入数据库

namespace listen;
use app\model\Log;

class LoginLog
{
	
    public function handle($params){
        $data['application_name'] = app('http')->getName();
		$data['weid'] = $params['weid'];
		$data['username'] = $params['user'];
		$data['url'] = request()->url(true);
		$data['ip'] = request()->ip();
		$data['useragent'] = request()->server('HTTP_USER_AGENT');
		$data['type'] = 1;
		
		Log::create($data);
    }
}