<?php

namespace app\model;

use think\Model;

class SigninConfig extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'signin_config';

    public static function datainitial()
    {
        $data = [
            [
                'day' => '第一天',
                'number' => 10,
                'status' => 1,
            ], [
                'day' => '第二天',
                'number' => 20,
                'status' => 1,
            ], [
                'day' => '第三天',
                'number' => 30,
                'status' => 1,
            ], [
                'day' => '第四天',
                'number' => 40,
                'status' => 1,
            ], [
                'day' => '第五天',
                'number' => 50,
                'status' => 1,
            ], [
                'day' => '第六天',
                'number' => 60,
                'status' => 1,
            ], [
                'day' => '第七天',
                'number' => 100,
                'status' => 1,
            ],
        ];

        $data =  self::setdata($data);
        self::createdata($data);
    }
    public static function setdata($data)
    {
        $weid = weid();
        if (!empty($data)) {
            foreach ($data as &$vo) {
                $vo['weid'] = $weid;
                if (empty($vo['sort'])) {
                    $vo['sort'] = 100;
                }
            }
        }
        return $data;
    }
    public static function createdata($data)
    {
        if (!empty($data)) {
            foreach ($data as $vo) {
                if (empty(self::where(['day' => $vo['day'], 'number' => $vo['number'], 'weid' => $vo['weid']])->find())) {
                    self::create($vo);
                }
            }
        }
        return $data;
    }
}
