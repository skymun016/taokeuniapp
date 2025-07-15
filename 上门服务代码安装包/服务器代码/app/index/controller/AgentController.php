<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Member;
use app\model\Config;
use app\model\QrcodeMod;
use app\model\Agent;
use app\model\AgentCode;

class AgentController extends Base
{
    public function amount()
    {
        $Membermob = new Member;
        $memberinfo = $Membermob->getUserByWechat();
        $data['base'] = $memberinfo;

        return $this->json(['data' => $data]);
    }

    public function poster()
    {
        $Membermob = new Member;
        $QrcodeMod = new QrcodeMod;
        $memberinfo = $Membermob->getUserByWechat();
        $Qrcodedata = $QrcodeMod->getUserQrcode($memberinfo['id'], 'pages/index/index');
        $config = Config::getconfig();
        $agent = Config::getconfig('agent');

        $data['sitename'] = $config['sys_title'];
        $data['poster'] = strongHttp(toimg($agent['poster']));
        $data['nickName'] = $memberinfo['nickname'];
        $data['qrcode'] = $Qrcodedata;

        return $this->json(['data' => $data]);
    }
    public function check()
    {
        $uid = UID();
        if (!empty($uid)) {
            $data = Agent::where(['uid' => $uid, 'status' => 1])->find();
            if (!empty($data)) {
                $data = $data->toArray();
            }

            $data['id'] = $data['id'];
            $data['is_submitaudit'] = \app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'));
            $data['agent_code'] = AgentCode::getagent_code($uid);
        } else {
            $data = [];
        }

        return $this->json(['msg' => '您还不是分销达人', 'data' => $data]);
    }

    public function checkreg()
    {
        $uid = UID();
        if (!empty($uid)) {
            $data = Agent::where(['uid' => $uid])->find();
            if (!empty($data)) {
                $data = $data->toArray();
            }
        }

        return $this->json(['data' => $data]);
    }

    public function upcode()
    {
        $agent_code = input('post.agent_code', '', 'serach_in');

        if (!empty($agent_code)) {
            $agent_code = AgentCode::upcode(UID(), $agent_code, 1);
        }
        if (empty($agent_code)) {
            return $this->json(['errno' => 1, 'message' => '这个邀请码已有人使，请换别的！', 'data' => '']);
        } else {
            return $this->json(['data' => $agent_code]);
        }
    }
}
