<?php

declare(strict_types=1);

namespace app;

use think\App;
use think\View;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use think\facade\Validate;
use app\model\Files;

/**
 * 控制器基础类
 */
abstract class BaseController
{

	/**
	 * Request实例
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * 应用实例
	 * @var \think\App
	 */
	protected $app;


	/**
	 * 是否批量验证
	 * @var bool
	 */
	protected $batchValidate = false;

	/**
	 * 控制器中间件
	 * @var array
	 */
	protected $middleware = [];

	public function __construct(App $app, View $view)
	{
		$this->app     = $app;
		$this->request = $this->app->request;
		$this->view = $view;

		// 控制器初始化
		$this->initialize();
	}

	// 初始化
	protected function initialize()
	{
	}

	protected function success($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
	{
		if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
			$url = $_SERVER["HTTP_REFERER"];
		} elseif ($url) {
			$url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url);
		}

		$result = [
			'code' => 1,
			'msg'  => $msg,
			'data' => $data,
			'url'  => $url,
			'wait' => $wait,
		];

		$type = $this->getResponseType();
		if ($type == 'html') {
			$response = view(config('app.dispatch_success_tmpl'), $result);
		} else if ($type == 'json') {
			$response = json($result);
		}

		throw new HttpResponseException($response);
	}

	protected function error($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
	{
		if (is_null($url)) {
			$url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
		} elseif ($url) {
			$url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : $this->app->route->buildUrl($url);
		}

		$result = [
			'code' => 0,
			'msg'  => $msg,
			'data' => $data,
			'url'  => $url,
			'wait' => $wait,
		];

		$type = $this->getResponseType();
		if ($type == 'html') {
			$response = view(config('app.dispatch_success_tmpl'), $result);
		} else if ($type == 'json') {
			$response = json($result);
		}

		throw new HttpResponseException($response);
	}

	protected function getResponseType()
	{
		return $this->request->isJson() || $this->request->isAjax() ? 'json' : 'html';
	}

	//上传前先检测上传模式 如果是oss客户端直传则直接返回 token 、key等信息
	public function upload()
	{

		$file = $this->request->file('file');

		$file_type = upload_replace(config('my.filetype')); //上传黑名单过滤

		if (!Validate::fileExt($file, $file_type)) {
			throw new ValidateException('文件类型验证失败');
		}

		if (!Validate::fileSize($file, config('my.filesize') * 1024 * 1024)) {
			throw new ValidateException('文件大小验证失败');
		}

		if ($url = $this->up($file)) {
			return $this->json(['data' => $url, 'url' => $url, 'weid' => weid()]);
		}
	}

	//开始上传
	protected function up($file)
	{
		$oss_settings = \app\model\OssUpload::getSettings();

		$is_local = $this->request->post('is_local');
		$cid = $this->request->post('cid');

		if (!empty($oss_settings['oss_accessKey']) && !empty($oss_settings['oss_secretKey']) && !empty($oss_settings['oss_bucket']) && !empty($oss_settings['oss_domain']) && $is_local != 1) {
			$url = \utils\oss\OssService::OssUpload($oss_settings, ['tmp_name' => $file->getPathname(), 'extension' => $file->extension()]);
		} else {

			//需要fileinfo
			//$filename = Filesystem::disk('public')->putFile($this->getFileName(), $file, 'uniqid');
			//需要fileinfo===end

			// 获取文件后缀
			$extension = strtolower($file->extension());
			// 文件保存目录
			$savePath = config('filesystem.disks.public.root') . '/' . $this->getFileName() . '/';
			// 新的文件名
			$filename = uniqid() . '.' . $extension;
			// 转移临时文件到指定目录
			$file->move($savePath, $filename);

			$upload_type = $this->request->post('upload_type');
			if ($upload_type == 'file') {
				$url = config('filesystem.disks.public.url') . '/' . $this->getFileName() . '/' . $filename;
			} else {
				$url = toimg(config('filesystem.disks.public.url') . '/' . $this->getFileName() . '/' . $filename);
			}
		}

		$source_id = $this->userInfo['id'] ?? UID();
		$file_type = upload_replace(config('my.filetype')); //上传黑名单过滤
		if (Validate::fileExt($file, $file_type)) {
			Files::create([
				'weid' => weid(),
				'cid' => (int) $cid,
				'type' => $upload_type,
				'name' => $filename,
				'uri' => $url,
				'source_id'   => (int) $source_id
			]);
		}

		return $url;
	}

	//获取上传的文件完整路径
	private function getFileName()
	{
		return  date(config('my.upload_subdir'));
	}

	//获取阿里云oss客户端上传地址
	private function getendpoint($oss_settings)
	{
		if (strpos($oss_settings['oss_domain'], 'aliyuncs.com') !== false) {
			if (strpos($oss_settings['oss_domain'], 'https') !== false) {
				$point = 'https://' . $oss_settings['oss_bucket'] . '.' . substr($oss_settings['oss_domain'], 8);
			} else {
				$point = 'http://' . $oss_settings['oss_bucket'] . '.' . substr($oss_settings['oss_domain'], 7);
			}
		} else {
			$point = $oss_settings['oss_domain'];
		}
		return $point;
	}

	//阿里云oss上传异步回调返回上传路径，放到这是因为这个地址必须外部能直接访问到
	function aliOssCallBack()
	{
		$oss_settings = \app\model\OssUpload::getSettings('ali');

		$body = file_get_contents('php://input');
		header("Content-Type: application/json");
		$url = $this->getendpoint($oss_settings['oss_domain']) . '/' . str_replace('%2F', '/', $body);
		return $this->json(['code' => 1, 'data' => $url, 'url' => $url]);
	}
}
