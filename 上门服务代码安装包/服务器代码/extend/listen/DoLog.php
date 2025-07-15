<?php

//操作日志写入数据库

namespace listen;

use app\model\Log;

class DoLog
{

	public function handle($user)
	{
		if (!empty($user)) {
			if (in_array(request()->action(), ['add', 'update', 'listUpdate', 'resetPwd', 'delete'])) {
				$content = request()->except(['s', '_pjax']);
				if ($content) {
					foreach ($content as $k => $v) {
						if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false) {
							unset($content[$k]);
						}
					}
				}

				$data['application_name'] = app('http')->getName();
				$data['weid'] = weid();
				$data['username'] = $user;
				$data['url'] = request()->url(true);
				$data['ip'] = request()->ip();
				$data['useragent'] = request()->server('HTTP_USER_AGENT');
				$data['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
				$data['type'] = 2;

				Log::create($data);
			}
		}
	}
}
