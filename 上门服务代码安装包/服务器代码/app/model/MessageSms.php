<?php

namespace app\model;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class MessageSms

{

	public static function itional_pay($orderInfo)
	{

		$param['uuid'] = OrderStaff::getuuid($orderInfo['id']);
		$param['uid'] = UuidRelation::getuid($param['uuid']);
		$param['title'] = '客户已支付尾款通知';
		$param['pages'] = '/pagesA/my/admintechnical/orderDetail';
		$param['query'] = 'id=' . $orderInfo['id'];

		self::sendsmsmessage($param);
	}
	//派单
	public static function distribution($uuid, $orderInfo)
	{
		$param['uuid'] = $uuid;
		$param['uid'] = UuidRelation::getuid($param['uuid']);
		$param['title'] = '平台派单通知';

		$param['pages'] = '/pagesA/my/admintechnical/orderDetail';
		$param['query'] = 'id=' . $orderInfo['id'];

		self::sendsmsmessage($param);
	}
	public static function storeOrderPay($sid, $orderInfo)
	{
		$param['uuid'] = Store::getUuid($sid);

		$param['uid'] = UuidRelation::getuid($param['uuid']);

		$param['title'] = '店铺订单通知';
		$param['content'] = '您有一个新的订单，请及时处理';
		$param['pages'] = '/pagesA/my/adminstore/orderDetail';
		$param['query'] = 'id=' . $orderInfo['id'];

		self::sendsmsmessage($param);
	}
	public static function undertake($orderInfo)
	{
		$technicalConfig = Config::getconfig('technical');
		if ($technicalConfig['is_pickuporder'] == 1) {

			$Technical = Technical::gelist_bycitycate($orderInfo);

			if (!empty($Technical)) {
				foreach ($Technical as $vo) {
					$param['uid'] = $vo['uid'];

					if (empty($param['uid'])) {
						$param['uid'] = UuidRelation::getuid($vo['uuid']);
					}
					$param['uuid'] = $vo['uuid'];
					$param['title'] = '平台推荐单通知';
					$param['pages'] = '/pagesA/my/publicOrder/order';

					self::sendsmsmessage($param);
				}
			}
		}
	}

	public static function orderPayNoticeAdmin($orderInfo)
	{

		$where['weid'] = weid();
		$where['sid'] = 0;
		$query = Users::where($where);
		$query->where('role_id', 'in', UsersRoles::getadminids());
		$query->whereNotNull('uuid');
		$query->where('uuid', '<>', '');

		$adminlist = $query->select()->toArray();
		//var_dump($query->getLastsql());

		if (!empty($adminlist)) {
			foreach ($adminlist as $vo) {

				$param['uid'] = UuidRelation::getuid($vo['uuid']);
				$param['uuid'] = $vo['uuid'];
				$param['title'] = '订单支付成功';

				$param['pages'] = '/pagesA/my/admin/orderDetail';
				$param['query'] = 'id=' . $orderInfo['id'];


				self::sendsmsmessage($param);
			}
		}
	}

	public static function sendsmsmessage($param)
	{
		$weid = weid();
		$miniConfig = Config::getconfig('miniprogram');
		if (empty($miniConfig['app_id'])) {
			$smslink = gethost() . scriptPath() .  '/h5/?i=' . $weid . '#/' . $param['pages'] . '?' . $param['query'];
		} else {
			$retlink = \app\samos\wechat\MiniProgram::urllink([
				'path' => $param['pages'],
				'query' => $param['query']
			]);

			$smslink = $retlink['url_link'];
		}

		$phone  = Technical::getTel($param['uuid']);
		if (!empty($phone)) {
			@$res =	MessageSms::send_sms([
				'phone' => $phone,
				'param' => json_encode([
					'customer' => "您有一个新的平台订单，请及时处理" . $smslink
				])
			]);
			Test::create(['title' => '发手机短信', 'info' => serialize($res)]);
		}
	}

	/**
	 * 发送短信
	 * @param $AccessKeyId
	 * @param $Secret
	 * @param $params
	 * @return bool
	 * @throws ClientException
	 */
	static function send_sms($params)
	{
		if (empty($params['phone'])) {
			return false;
		}
		$smsConfig = Config::getconfig('sms');
		if (empty($smsConfig['status']) || empty($smsConfig['AccessKeyId'])) {
			return false;
		}

        // 创建客户端
		AlibabaCloud::accessKeyClient(trim($smsConfig['AccessKeyId']), trim($smsConfig['Secret']))
			->regionId('cn-hangzhou')
			->asDefaultClient();
		try {
			$result = AlibabaCloud::rpc()
				->product('Dysmsapi')
				->version('2017-05-25')
				->action('SendSms')
				->host('dysmsapi.aliyuncs.com')
				->options([
					// 这里的参数可以在openAPI Explorer里面查看
					'query' => [
						'RigionId'     => 'cn_hangzhou',
						'PhoneNumbers' => $params['phone'],	// 输入的手机号
						'SignName'     => trim($smsConfig['SignName']),	// 签名信息
						'TemplateCode' => trim($smsConfig['TemplateCode']),	// 短信模板id
						'TemplateParam' => $params['param']	// 可选，模板变量值，json格式
					]
				])
				->request();
			//print_r($result->toArray());
			Test::create(['title' => '手机短信', 'info' => serialize($result->toArray())]);
			return $result->toArray();
		} catch (ClientException $e) {
			return $e->getErrorMessage() . PHP_EOL;
		} catch (ServerException $e) {
			return $e->getErrorMessage() . PHP_EOL;
		}
	}
}
