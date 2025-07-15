<?php

namespace app\index\controller;

use think\facade\Request;
use app\model\UsersSessions;
use app\model\UuidRelation;
use app\model\TextReplace;

class Base extends \app\BaseController
{
    protected $userInfo = [];

    protected function initialize()
    {
        $this->checkTokenAuth();
    }

    protected function checkTokenAuth()
    {
        global $_W;
        $token = $this->getAppToken();

        $ptype = getclient();
        $data['api'] = Author()::getlan();
        $tokenInfo = UsersSessions::where(['token' => $token, 'ptype' => $ptype])->find();
        if (!empty($tokenInfo)) {
            $tokenInfo = $tokenInfo->toArray();
            $this->userInfo = iunserializer($tokenInfo['data']);

            if (empty($this->userInfo['nickname']) || empty($this->userInfo['avatar'])) {
                if (!empty($this->userInfo['openid'])) {
                    $uid =  \app\model\Openid::getuidbyopenid($this->userInfo['openid']);
                    if (!empty($uid)) {
                        $member = \app\model\Member::find($uid);
                        if (!empty($member)) {
                            $this->userInfo['nickname'] = $member->nickname;
                            $this->userInfo['avatar'] = $member->userpic;
                        }
                    }
                }
            }
            if (empty($_W['uniacid']) && !empty($tokenInfo['weid'])) {
                $_W['uniacid'] = $tokenInfo['weid'];
            }

            if (!empty($this->userInfo['cityinfo']['ocid'])) {
                $_W['ocid'] = $this->userInfo['cityinfo']['ocid'];
            }

            if (!empty($this->userInfo['cityinfo']['tz_id'])) {
                $_W['tz_id'] = $this->userInfo['cityinfo']['tz_id'];
            }

            if (empty($_W['fans']['openid']) && !empty($this->userInfo['openid'])) {
                $_W['fans']['openid'] = $this->userInfo['openid'];
            }
            $_W['fans']['ptype'] = $ptype;
            event('DoLog', $this->userInfo['username']);    //写入操作日志
        } else {
            if (!empty($_W['fans']['openid'])) {
                $data['openid'] = $_W['fans']['openid'];
            }
            $data['ptype'] = $ptype;
            $data['weid'] = weid();
            $this->setAppToken($data, $token);
        }
    }

    //设置token
    protected function setAppToken($data, $token = '')
    {

        $weid = $data['weid'];

        if (empty($weid)) {
            $weid = weid();
        }

        if (empty($token)) {
            $token = md5(uniqid());
        }

        if (empty($data['ptype'])) {
            $data['ptype'] = getclient();
        }
        $ptype = $data['ptype'];



        $data['api'] = Author()::getlan();
        //登录的时候把token写入数据表
        $tokenInfo = UsersSessions::where(['token' => $token, 'ptype' => $ptype])->find();
        if (empty($tokenInfo)) {
            UsersSessions::create([
                'weid' => $weid,
                'token' => $token,
                'ptype' => $ptype,
                'ip' => getRealIP(),
                'expire_time' => time(),
                'data' => serialize($data),
                'status' => 1
            ]);
        } else {
            UsersSessions::where(['token' => $token, 'ptype' => $ptype])->update([
                'weid' => $weid,
                'token' => $token,
                'ptype' => $ptype,
                'ip' => getRealIP(),
                'expire_time' => time(),
                'data' => serialize($data),
                'status' => 1
            ]);
        }

        return $token;
    }
    //取token
    protected function getAppToken()
    {
        $Authorization = Request::header('Authorization');
        $state = input('param.xmtoken', '', 'serach_in');

        if (empty($state)) {
            $state = $Authorization;
        }

        if (empty($state)) {
            $state = input('get.state', '', 'serach_in');
        }

        if (empty($state)) {
            $state = input('param.state', '', 'serach_in');
        }

        $token = str_replace("we7sid-", "", $state);

        return $token;
    }

    protected function json($result)
    {
        if (empty($result['errno'])) {
            $result['errno'] = 0;
        }
        if(empty($result['no_replace'])){
            $result = TextReplace::setreplace($result);
        }
        return json($result);
    }
}
