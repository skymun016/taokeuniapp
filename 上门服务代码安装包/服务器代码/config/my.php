<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 自定义配置
// +----------------------------------------------------------------------
return [
	'app_v2'          => 'xm_ma' . 'll',
	'app_v3'          => 'xm_ma' . 'llv3',
	'app_v6'          => 'xm_ma' . 'llv6',
	'upload_subdir'		=> 'Ym',				//文件上传二级目录 标准的日期格式
	'nocheck'			=> ['admin/Login/index', 'admin/Login/sitesetup', 'admin/Login/logout'],	//不需要验证权限的url
	'testnoupdate'		=> [
		'admin/config/update',
		'admin/diypage/update',
		'admin/diypage/setindex',
		'admin/diypage/setaudit',
		'admin/diypage/delete',
		'admin/registerfield/listUpdate',
		'admin/registerfield/update',
		'admin/registerfield/delete',
		'admin/Category/delete',
		'admin/goods/delete',
		'admin/member/delete',
		'admin/Upgrade/index',
		'admin/member/dumpdata',
		'admin/Category/update',
		'admin/Category/delete',
		'admin/bottommenu/listUpdate',
		'admin/bottommenu/update',
		'admin/bottommenu/delete',
		'admin/technical/delete',
		'admin/platform/delete',
		'admin/Users/delete',
		'admin/Usersroles/delete'
	],	//演示站不能修改的url
	'error_log_code'	=> 500,					//写入日志的状态码
	'dump_extension'	=> 'xlsx',				//默认导出格式
	'filetype'	=> 'jpg,jpeg,png,gif,mp4,3gp,m3u8,doc,docx,xls,xlsx,pem',  //上传文件文件类型
	'filesize'	=> 50,				            //上传文件最大限制(M)
	'check_file_status'	=> true,			//上传图片是否检测图片存在
	'authkey'	=>  '655ac7fe',			    //密码加密附加码
];
