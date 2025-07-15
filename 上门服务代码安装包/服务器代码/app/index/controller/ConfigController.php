<?php

namespace app\index\controller;

use think\exception\ValidateException;
use app\model\Config;

class ConfigController extends Base
{
    public function index()
    {

        $mo = input('post.mo', '', 'serach_in');
        if (empty($mo)) {
            $mo = 'common';
        }

        $data = Config::getconfig($mo);

        if ($mo == 'member') {
            $data['sms_status'] = Config::getconfig('sms')['status'];
        }

        if ($mo == 'pagestyle') {
            if (empty($data['appstylecolor'])) {
                $data['appstylecolor'] = '#ff0000';
            }
        }

        $data['SITE_ICON'] = toimg($data['SITE_ICON']);
        $data['poster'] = toimg($data['poster']);
        $data['applypic'] = toimg($data['applypic']);
        $data['module'] = config('database.app_name');
        $data['is_submitaudit'] = \app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'));

        $data['kefu'] = Config::getconfig('kefu');

        return $this->json(['data' => $data]);
    }

    public function keyword()
    {
        $config = Config::getconfig('common');
        //空判断
        if (!empty($config['keyword'])) {
            $data['keyword'] = explode(',', $config['keyword']);
        }

        return $this->json(['data' => $data]);
    }

    public function memberislogin()
    {
        $data = [];
        $is_submitaudit = \app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'));

        if ($is_submitaudit != 1) {
            $data = Config::getconfig('member');
        }
        return $this->json(['data' => $data]);
    }

    public function audit()
    {
        $data['is_submitaudit'] = \app\model\Uploadminiprogram::getaudit(input('get.v', '', 'serach_in'));
        return $this->json(['data' => $data]);
    }
    public function is_v2()
    {
        if (config('database.app_name') == config('my.app_v2')) {
            return $this->json(['data' => 1]);
        } else {
            return $this->json(['data' => 0]);
        }
    }
    public function is_v3()
    {
        if (config('database.app_name') == config('my.app_v3')) {
            return $this->json(['data' => 1]);
        } else {
            return $this->json(['data' => 0]);
        }
    }
    public function is_v6()
    {
        if (config('database.app_name') == config('my.app_v6')) {
            return $this->json(['data' => 1]);
        } else {
            return $this->json(['data' => 0]);
        }
    }
}
