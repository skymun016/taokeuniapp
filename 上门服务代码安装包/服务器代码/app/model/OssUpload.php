<?php

namespace app\model;

use think\Model;

class OssUpload extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'oss_upload';

    public static function datainitial()
    {
        $weid = weid();
        $data[1] = [
            'weid' => $weid,
            'code' => 'qiniuyun',
            'title' => '七牛云oss',
            'settings' => '',
            'status' => 0,
            'sort' => 10,
        ];
        $data[2] = [
            'weid' => $weid,
            'code' => 'ali',
            'title' => '阿里云oss',
            'settings' => '',
            'status' => 0,
            'sort' => 20,
        ];
        $data[3] = [
            'weid' => $weid,
            'code' => 'qcloud',
            'title' => '腾讯云oss',
            'settings' => '',
            'status' => 0,
            'sort' => 20,
        ];


        foreach ($data as $vo) {
            if (!(self::where(['weid' => $weid, 'code' => $vo['code']])->find())) {
                self::create($vo);
            }
        }
    }
    public static function getSettings($code = '')
    {
        $where['status'] = 1;
        if ($code) {
            $where['code'] = $code;
        }

        $data = self::where(['weid' => weid()])->where($where)->order('id desc')->find();

        if (empty($data)) {
            $data = self::where(['weid' => 0])->where($where)->order('id desc')->find();
        }
        if (!empty($data)) {
            $data = $data->toArray();
            $data['settings'] = iunserializer($data['settings']);
            $data['settings']['status'] = $data['status'];
            $data['settings']['code'] = $data['code'];
        }
        return $data['settings'];
    }
}
