<?php

namespace app\model;

use think\Model;

class Lingqunum extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'lingqunum';

    public static function upnum($uid)
    {
        $weid = weid();

        $Config =  Config::getconfig();
        $lingqu_num = (int)$Config['lingqu_num'];

        $lingquArray =  self::where(['weid' => $weid, 'lingqudate' => time_ymd(), 'uid' => $uid])->find();

        if (empty($lingquArray)) {

            $lingquArray = $lingquArray->toArray();
            $updata = [
                'weid' => $weid,
                'uid' => $uid,
                'num' => 1,
                'lingqudate' => time_ymd(),
                'creat_time' => time()
            ];

            self::create($updata);
            return 1;
        } else {

            if ($lingqu_num > $lingquArray['num']) {
                self::where('id', $lingquArray['id'])->inc('num')->update();
                return 1;
            } else {
                return 0;
            }
        }
    }

    public static function checknum($uid)
    {
        $weid = weid();

        $Config =  Config::getconfig();
        $lingqu_num = (int)$Config['lingqu_num'];

        $lingquArray =  self::where(['weid' => $weid, 'lingqudate' => time_ymd(), 'uid' => $uid])->find();

        if (empty($lingquArray)) {
            return 1;
        } else {
            $lingquArray = $lingquArray->toArray();
            if ($lingqu_num > $lingquArray['num']) {
                return 1;
            } else {
                return 0;
            }
        }
    }
}
