<?php

namespace app\model;

use think\Model;

class RecoveryWeight extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'recovery_weight';

    public static function datainitial()
    {
        $weid = weid();
        $data = [
            [
                'weid' => $weid,
                'begin_weight' => '0',
                'end_weight' => '10',
                'status' => 1,
                'sort' => 100,
            ],
            [
                'weid' => $weid,
                'begin_weight' => '10',
                'end_weight' => '20',
                'status' => 1,
                'sort' => 100,
            ],
            [
                'weid' => $weid,
                'begin_weight' => '20',
                'end_weight' => '50',
                'status' => 1,
                'sort' => 100,
            ],
            [
                'weid' => $weid,
                'begin_weight' => '50',
                'end_weight' => '100',
                'status' => 1,
                'sort' => 100,
            ],
            [
                'weid' => $weid,
                'begin_weight' => '100',
                'end_weight' => '0',
                'status' => 1,
                'sort' => 100,
            ]
        ];

        foreach ($data as $vo) {
            if (!(self::where(['weid' => $weid, 'begin_weight' => $vo['begin_weight'], 'end_weight' => $vo['end_weight']])->find())) {
                self::create($vo);
            }
        }
    }
}
