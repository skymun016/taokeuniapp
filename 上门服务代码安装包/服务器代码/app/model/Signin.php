<?php

namespace app\model;

use think\Model;

class Signin extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'signin';

    public static function getSignIndex()
    {
        $where['weid'] = weid();
        $where['uid'] = UID();
        $data = self::where($where)
            ->order('id desc')
            ->limit(6)
            ->select()
            ->toArray();

        $sign_index = 0;
        $day = 1;

        if (!empty($data)) {
            foreach ($data as $vo) {
                if (date('Y-m-d', strtotime($vo['create_time'])) == date('Y-m-d', strtotime('now'))) {
                    $sign_index++;
                    $day++;
                } elseif (date('Y-m-d', strtotime($vo['create_time'])) == date('Y-m-d', strtotime('-' . $day . ' days'))) {
                    $sign_index++;
                    $day++;
                }
            }
        }

        return $sign_index;
    }

    public static function getSumSginDay()
    {
        $where['weid'] = weid();
        $where['uid'] = UID();
        $sum_sgin_day = self::where($where)->count();
        return $sum_sgin_day;
    }

    public static function getIsDaySgin()
    {
        $where['weid'] = weid();
        $where['uid'] = UID();
        $data = self::where($where)
            ->order('id desc')
            ->find();
        $res = false;
        
        if (!empty($data)) {
            if (date('Y-m-d', strtotime($data->create_time)) == date('Y-m-d', strtotime('now'))) {
                $res = true;
            }
        }

        return $res;
    }

    public static function getSginNumber()
    {
        $where['weid'] = weid();
        $data = SigninConfig::where($where)
            ->order('sort asc')
            ->select()
            ->toArray();

        $sign_index = self::getSignIndex();

        $res = $data[$sign_index]['number'];

        if (empty($res)) {
            $res = $data[0]['number'];
        }

        return $res;
    }
}
