<?php

namespace app\model;

use think\Model;

class Uploadminiprogram extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'uploadminiprogram';

    public static function getnewversion($ptype)
    {
        $weid = weid();
        $newversion = self::where(['weid' => $weid, 'ptype' => $ptype, 'is_up' => 0])->order('id desc')->find();

        if (!empty($newversion)) {
            $newversion = $newversion->toArray();
            return $newversion;
        } else {

            $preversion = self::where(['weid' => $weid, 'ptype' => $ptype, 'status' => 1])->order('id desc')->find();

            if ($preversion) {

                $updata['version'] = versionincreasing($preversion->version);

                $updata['desctext'] = $preversion->desctext;
            } else {
                $updata['version'] = '1.0.0';
                $updata['desctext'] = '修复了BUG';
            }

            $updata['weid'] = $weid;
            $updata['ptype'] = $ptype;
            $updata['is_up'] = 0;
            $updata['status'] = 1;

            $newversion = self::create($updata);
            return $newversion->toArray();
        }
    }
    public static function getaudit($ver)
    {
        if (input('get.from') != 'wxapp') {
            return 0;
        }
        if(empty($ver)){
            return 0;
        }
        $version = self::find($ver);
        if ($version && $version->weid == weid()) {
            if ($version->status == 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
}
