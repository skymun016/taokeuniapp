<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Log;
use think\facade\Db;

class LogController extends Base
{


	/*
 	* @Description  数据列表
 	*/
	function index()
	{
		$username = input('post.username', '', 'serach_in');
		$type = input('post.type', '', 'serach_in');
		$create_time = input('post.create_time', '', 'serach_in');

		$where = [];
		$where['weid'] = weid();
		if (!empty($username)) {
			$where['username'] = $username;
		}

		if (!empty($type)) {
			$where['type'] = $type;
		}

		$field = 'id,application_name,username,url,ip,create_time,type';

		$query = Log::where($where);

		if (!empty($create_time)) {
			$query->where('create_time','between', [strtotime($create_time[0]), strtotime($create_time[1])]);
		}
		$res = $query->field($field)->order('id desc')->paginate(getpage())->toArray();

		$data['data'] = $res;
		return $this->json($data);
	}


	/*
 	* @Description  删除
 	*/
	function delete()
	{
		return $this->del(new Log());
	}


	/*
 	* @Description  查看详情
 	*/
	function detail()
	{
		$id =  $this->request->post('id', '', 'serach_in');
		if (!$id) throw new ValidateException('参数错误');
		$field = 'id,application_name,username,url,ip,useragent,content,errmsg,create_time,type';
		$res = Log::field($field)->find($id);
		return $this->json(['data' => $res]);
	}


	function dumpdata()
	{
		$page = $this->request->post('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;
		$username = input('post.username', '', 'serach_in');
		$type = input('post.type', '', 'serach_in');
		$create_time = input('post.create_time', '', 'serach_in');

		$where = [];
		if (!empty($username)) {
			$where['username'] = $username;
		}

		if (!empty($type)) {
			$where['type'] = $type;
		}

		$field = 'id,application_name,username,url,ip,useragent,content,errmsg,create_time,type';

		$query = Log::where($where);

		if (!empty($create_time)) {
			$query->where('create_time','between', [strtotime($create_time[0]), strtotime($create_time[1])]);
		}

		$count = $query->count();

		$res = $query->field($field)->order('id desc')->limit(($page - 1) * $limit, $limit)->select()->toArray();
        //var_dump($query->getLastSql());

		foreach ($res as $key => $val) {
			$res[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
			$res[$key]['type'] = getItemVal($val['type'], '[{"key":"登录日志","val":"1","label_color":"info"},{"key":"操作日志","val":"2","label_color":"warning"},{"key":"异常日志","val":"3","label_color":"danger"}]');
		}
		$data['header'] = explode(',', '编号,应用名,用户名,请求url,客户端ip,浏览器信息,请求内容,异常信息,创建时间,类型');
		$data['percentage'] = ceil($page * 100 / ceil($count / $limit));
		$data['filename'] = '日志管理.' . config('my.dump_extension');
		$data['data'] = $res;
		return $this->json($data);
	}
}
