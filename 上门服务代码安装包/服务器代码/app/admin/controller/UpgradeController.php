<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\RegisterField;
use app\model\Address;
use app\model\Area;
use app\model\Order;
use app\model\Goods;
use app\model\GoodsToCategory;
use app\model\Config;
use app\model\Lang;

class UpgradeController extends Base
{

	public function index()
	{

		if (!file_exists($this->getRoot() . '/data/install.lock')) {
			$this->mkdataconfig([
				'server' => config('database.connections.mysql.hostname'),
				'username' => config('database.connections.mysql.username'),
				'password' => config('database.connections.mysql.password'),
				'port' => config('database.connections.mysql.hostport'),
				'name' => config('database.connections.mysql.database'),
				'prefix' => str_replace(config('database.app_name') . "_", "", config('database.connections.mysql.prefix'))
			]);
			$this->mkLockFile();
		}

		$url = 'http://console.samcms.com/public/index.php/index/authorization';

		$postparam = Author()::authorizationInfo();
		$postparam['biaoshi'] = config('database.app_name');
		$result = vit_http_request($url, $postparam);

		$result = is_null(json_decode($result)) ? [] : json_decode($result, true);

		if (($result['data']['secretarray'])) {
			$mo = 'win' . 'ger';
			$configstr = serialize($result['data']['secretarray']);
			$config = Config::where(['weid' => 0, 'module' => $mo])->find();
			if (!empty($config)) {
				$config = $config->toArray();
				Config::update(['settings' => $configstr], ['id' => $config['id']]);
			} else {
				$data["weid"] = 0;
				$data["module"] = $mo;
				$data["status"] = 1;
				$data["settings"] = $configstr;
				Config::create($data);
			}
			$configaut = Config::where(['weid' => 0, 'module' => 'aut'])->find();
			if (empty($configaut)) {
				$data["weid"] = 0;
				$data["module"] = 'aut';
				$data["status"] = 1;
				$data["settings"] = serialize(['aut' => 1]);
				Config::create($data);
			}
		}

		if (($result['data']['backendphp'])) {
			$this->upvendor();
			$file_url = $result['data']['backendphp']; //更新包的下载地址
		} else {
			throw new ValidateException('更新包不存在！');
		}

		$filename = basename($file_url); //更新包文件名称

		// 检查和创建文件夹
		$dir = $this->getRoot() . "/upgrade/";
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		// 下载更新包到本地并赋值文件路径变量
		$path = file_exists($dir . $filename) ? $dir . $filename : $this->download_file($file_url, $dir, $filename);

		// 如果下载没成功就返回报错
		if (!file_exists($dir . $filename)) {
			throw new ValidateException("文件下载失败！");
		}

		// PHP解压的扩展类
		if (!class_exists("\ZipArchive")) {
			throw new ValidateException("请安装ZipArchive扩展！");
		}

		// 实例化ZipArchive
		$zip = new \ZipArchive();

		//打开压缩包
		if ($zip->open($path) === true) {
			// 复制根目录
			$toPath = $this->getRoot() . "/";
			try {
				// 解压文件到toPath路径下，用于覆盖差异文件
				$zipres =	$zip->extractTo($dir);
				// 必须销毁变量，否则会报错
				unset($zip);
				// 删除更新包
				unlink($path);
				if (file_exists($dir . 'admin/')) {
					$this->deleteDir($toPath . 'admin/');
				}
				if (file_exists($dir . 'h5/')) {
					$this->deleteDir($toPath . 'h5/');
				}
				$this->xCopy($dir, $toPath, 1);
				$this->deleteDir($dir);

				//更新数据库
				$files = dirname(dirname(dirname(__DIR__))) . '/upgrade.php';
				include $files;
				RegisterField::datainitial('member');
				RegisterField::datainitial('agent');
				RegisterField::datainitial('technical');
				RegisterField::datainitial('store');
				Lang::datainitial();

				$Addressdata = Address::where('city_name', '=', '')->where('city_id', '>', 0)->select()->toArray();

				if (!empty($Addressdata)) {
					foreach ($Addressdata as $avo) {
						if (empty($avo['city_name'])) {
							if (!empty($avo['province_id'])) {
								$province_name = Area::get_area_name($avo['province_id']);
								if (!empty($province_name)) {
									Address::where('id', $avo['id'])->update([
										'province_name' => $province_name,
									]);
								}
							}

							if (!empty($avo['city_id'])) {
								$city_name = Area::get_area_name($avo['city_id']);
								if (!empty($city_name)) {
									Address::where('id', $avo['id'])->update([
										'city_name' => $city_name,
									]);
								}
							}

							if (!empty($avo['district_id'])) {
								$district_name = Area::get_area_name($avo['district_id']);
								if (!empty($district_name)) {
									Address::where('id', $avo['id'])->update([
										'district_name' => $district_name,
									]);
								}
							}
						}
					}
				}

				/*太老的版本升级需要行以下代码
				$Orderdata = Order::where('shipping_city_name', '=', '')->where('shipping_city_id', '>', 0)->select()->toArray();
				if (!empty($Orderdata)) {
					foreach ($Orderdata as $ovo) {
						if (empty($ovo['shipping_city_name'])) {
							Order::where('id', $ovo['id'])->update([
								'shipping_province_name' => Area::get_area_name($ovo['shipping_province_id']),
								'shipping_city_name' => Area::get_area_name($ovo['shipping_city_id']),
								'shipping_district_name' => Area::get_area_name($ovo['shipping_district_id'])
							]);
						}
					}
				}*/

				$Goodsdata = Goods::where('cat_id', 0)->select()->toArray();
				if (!empty($Goodsdata)) {
					foreach ($Goodsdata as $gvo) {
						$gtc = GoodsToCategory::where('goods_id', $gvo['id'])->find();
						if ($gtc) {
							Goods::where('id', $gvo['id'])->update(['cat_id' => $gtc->category_id]);
						}
					}
				}

				@unlink($files);
				// 更新完成
				return $this->json(['data' => '版本更新完成！!']);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage() . "或没有[" . $dir . "]目录的写入权限");
			}
		} else {
			// 压缩包打开失败，删除文件并且返回报错
			unlink($path);
			throw new ValidateException('更新包解压失败，请重试');
		}
	}

	public function upvendor()
	{

		$file_url = 'https://w7pic2.samcms.com/2023716/vendor.zip'; //vendor更新包的下载地址

		$filename = basename($file_url); //更新包文件名称

		// 检查和创建文件夹
		$dir = $this->getRoot() . "/upgradevendor/";
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		// 下载更新包到本地并赋值文件路径变量
		$path = file_exists($dir . $filename) ? $dir . $filename : $this->download_file($file_url, $dir, $filename);

		// 如果下载没成功就返回报错
		if (!file_exists($dir . $filename)) {
			throw new ValidateException("vendor文件下载失败！");
		}

		// PHP解压的扩展类
		if (!class_exists("\ZipArchive")) {
			throw new ValidateException("请安装ZipArchive扩展！");
		}

		// 实例化ZipArchive
		$zip = new \ZipArchive();

		//打开压缩包
		if ($zip->open($path) === true) {
			// 复制根目录
			$toPath = $this->getRoot() . "/";
			try {
				// 解压文件到toPath路径下，用于覆盖差异文件
				$zipres =	$zip->extractTo($dir);
				// 必须销毁变量，否则会报错
				unset($zip);
				// 删除更新包
				unlink($path);
				if (file_exists($dir . 'vendor/')) {
					$this->deleteDir($toPath . 'vendor/');
				}
				$this->xCopy($dir, $toPath, 1);
				$this->deleteDir($dir);
			} catch (\Exception $e) {
				throw new ValidateException($e->getMessage() . "或没有[" . $dir . "]目录的写入权限");
			}
		} else {
			// 压缩包打开失败，删除文件并且返回报错
			unlink($path);
			throw new ValidateException('更新包解压失败，请重试');
		}
	}

	/*
    * 文件下载方法
    * $url 文件下载地址
    * $dir 存储的文件夹
    * $filename 文件名字
    */
	public function download_file($url, $dir, $filename = '')
	{
		if (empty($url)) {
			return false;
		}
		$ext = strrchr($url, '.');

		$dir = realpath($dir);
		//目录+文件
		$filename = (empty($filename) ? '/' . time() . '' . $ext : '/' . $filename);
		$filename = $dir . $filename;
		//开始捕捉
		ob_start();

		try {
			readfile($url);
		} catch (\Exception $e) {
			throw new ValidateException('文件下载失败，请联系开发人员!');
		}

		$img = ob_get_contents();
		ob_end_clean();
		$size = strlen($img);
		$fp2 = fopen($filename, "a");
		fwrite($fp2, $img);
		fclose($fp2);
		return $filename;
	}

	// xCopy("feiy","feiy2",1):拷贝feiy下的文件到 feiy2,包括子目录
	// xCopy("feiy","feiy2",0):拷贝feiy下的文件到 feiy2,不包括子目录
	//参数说明：
	// $source:源目录名
	// $destination:目的目录名
	// $child:复制时，是不是包含的子目录
	public function xCopy($source, $destination, $child = 1)
	{

		if (!is_dir($source)) {
			echo ("Error:the $source is not a direction!");
			return 0;
		}
		if (!is_dir($destination)) {
			mkdir($destination, 0777);
		}
		$handle = dir($source);
		while ($entry = $handle->read()) {
			if (($entry != ".") && ($entry != "..")) {
				if (is_dir($source . "/" . $entry)) {
					if ($child)
						$this->xCopy($source . "/" . $entry, $destination . "/" . $entry, $child);
				} else {
					copy($source . "/" . $entry, $destination . "/" . $entry);
				}
			}
		}
		//return 1;
	}

	function deleteDir($dir)
	{
		if (!$handle = @opendir($dir)) {
			return false;
		}
		while (false !== ($file = readdir($handle))) {
			if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
				$file = $dir . '/' . $file;
				if (is_dir($file)) {
					$this->deleteDir($file);
				} else {
					@unlink($file);
				}
			}
		}
		@rmdir($dir);
	}

	public function mkLockFile()
	{
		return touch($this->getRoot() . '/data/install.lock');
	}
	public function mkdataconfig($db)
	{
		$config = include $this->getRoot() . '/data/example.php';

		$config = str_replace(array(
			'{db-server}', '{db-username}', '{db-password}', '{db-port}', '{db-name}', '{db-tablepre}'
		), array(
			$db['server'], $db['username'], $db['password'], $db['port'], $db['name'], $db['prefix']
		), $config);

		return file_put_contents($this->getRoot() . '/data/config.php', $config);
	}
}
