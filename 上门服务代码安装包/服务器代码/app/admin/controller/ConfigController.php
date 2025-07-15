<?php

namespace app\admin\controller;

use think\exception\ValidateException;
use app\model\Config;
use app\model\MemberAuthGroup;
use app\model\OrderStatus;
use app\samos\wechat\SubscribeMessage;
use app\samos\wechat\Messagetpl;

class ConfigController extends Base
{

	public function update()
	{
		$config = $this->request->post();
		$mo = $config['mo'];
		if (empty($mo)) {
			$mo = 'common';
		}
		if ($mo == 'common') {
			if (!empty($config['keyword'])) {
				$config['keyword'] = implode(',', $config['keyword']);
			}
		}

		if ($mo == 'collect') {
			if (!empty($config['collect_type'])) {
				$config['collect_type'] = implode(',', $config['collect_type']);
			}
		}
		if ($mo == 'miniprogram') {
			if (!empty($config['app_id'])) {
				if ($config['app_id'] == $config['techapp_id']) {
					throw new ValidateException("用户端小程序AppId和师傅端AppId不能相同！");
				}
			}
		}

		$configstr = serialize($config);

		if (empty($config['id'])) {
			$data["weid"] = weid();
			$data["module"] = $mo;
			$data["status"] = 1;
			$data["settings"] = $configstr;
			Config::create($data);
		} else {
			Config::update(['settings' => $configstr], ['id' => $config['id']]);
		}

		return $this->json(['msg' => '操作成功']);
	}

	function getInfo()
	{
		$mo = input('post.mo', '', 'serach_in');
		if (empty($mo)) {
			$mo = 'common';
		}
		$res = Config::getconfig($mo);
		$res['mo'] = $mo;
		if ($mo == 'common') {
			$res['logo'] = toimg($res['logo']);
			if (!empty($res['keyword'])) {
				$res['keyword'] = explode(',', $res['keyword']);
			}

			$res['autoapi'] = gethost() . scriptPath() . '/public/index.php/index/auto?i=' . weid();
		}
		if ($mo == 'mp') {
			$res['h5url'] = gethost() . scriptPath() . "/h5/?i=" . weid();
		}
		if ($mo == 'messagetpl') {
			$res['ordertploption'] = getordertploption();
		}

		if ($mo == 'collect') {
			if (empty($res['collect_type'])) {
				$res['collect_type'] = ['bank'];
			} else {
				if (!empty($res['collect_type'])) {
					$res['collect_type'] = explode(',', $res['collect_type']);
				}
			}
		}
		return $this->json(['data' => $res]);
	}

	function getField()
	{
		$data['member_auth_group'] = MemberAuthGroup::getpcarray();
		$data['collect_typearray'] = getcollect_type();

		return $this->json(['data' => $data]);
	}
	public function getsubscribemessage()
	{
		$tpl = input('post.tpl', '', 'serach_in');
		$mo = 'subscribemessage';
		$res = Config::getconfig($mo);
		$res['mo'] = $mo;
		$app = \app\samos\wechat\MiniProgram::makemini();
		$sm = SubscribeMessage::addparam($tpl);

		$Templates = $app->subscribe_message->getTemplates();
		foreach ($Templates['data'] as $vo) {
			if ($vo['title'] == $sm['title']) {
				$res[$tpl] = $vo['priTmplId'];
				break;
			}
		}

		if (empty($res[$tpl])) {

			$result =	$app->subscribe_message->addTemplate($sm['tid'], $sm['kidList'], $sm['sceneDesc']);

			if ($result['errmsg'] == 'ok') {
				$res[$tpl] = $result['priTmplId'];
			} else {
				throw new ValidateException("获取失败！" . $result['errcode']);
			}
		}
		if (empty($res['id'])) {
			$data["weid"] = weid();
			$data["module"] = $mo;
			$data["status"] = 1;
			$data["settings"] = serialize($res);
			Config::create($data);
		} else {
			Config::update(['settings' => serialize($res)], ['id' => $res['id']]);
		}

		$res = Config::getconfig($mo);

		return $this->json(['data' => $res]);
	}
	public function getmessagetpl()
	{
		$tpl = input('post.tpl', '', 'serach_in');
		$mo = 'messagetpl';
		$res = Config::getconfig($mo);
		$res['mo'] = $mo;
		$app = Messagetpl::maketpl();

		if (empty($app)) {
			throw new ValidateException("请先配置公众号！");
		}
		$sm = Messagetpl::addparam($tpl);
		$Templates = $app->getAllPrivateTemplate();
		//var_dump($Templates);
		$is_oldtpl = true;
		foreach ($Templates['template_list'] as $vo) {
			if ($vo['title'] == $sm['title']) {
				$res[$tpl] = $vo['template_id'];
				$is_oldtpl = false;
				break;
			}
		}

		if ($is_oldtpl) {

			$result =	$app->addTemplate($sm['tid'], $sm['keyword_name_list']);

			if ($result['errmsg'] == 'ok') {
				$res[$tpl] = $result['template_id'];
			} else {
				throw new ValidateException("获取失败！" . $result['errcode']);
			}
		}
		if (empty($res['id'])) {
			$data["weid"] = weid();
			$data["module"] = $mo;
			$data["status"] = 1;
			$data["settings"] = serialize($res);
			Config::create($data);
		} else {
			Config::update(['settings' => serialize($res)], ['id' => $res['id']]);
		}

		$res = Config::getconfig($mo);

		return $this->json(['data' => $res]);
	}
}
